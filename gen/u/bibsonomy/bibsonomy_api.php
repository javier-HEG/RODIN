<?php

#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Lesser General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Lesser General Public License for more details.
#
# You should have received a copy of the GNU Lesser General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
#

# $Id: bibsonomy_api.php,v 1.7 2008-06-24 14:23:44 cschenk Exp $

require_once('bibsonomy_def.php');
require_once('bibsonomy_cache.php');
require_once('bibsonomy_model.php');
require_once('bibsonomy_helper.php');
require_once('bibsonomy_xml_helper.php');
//FRI: Necessary change in order to get the authentification 
//     for the HEG Server run: Use own methods (see below)
//include_once("../u/FRIutilities.php");

/*
 * Returns instances of the BibSonomy class, either with or without the caching
 * mechanism enabled.
 */
class BibSonomyFactory {

	public static function produce($username, $password) {
		$bibsonomy = new BibSonomy($username, $password);

		$cache = new BibSonomyCache($bibsonomy);
		if ($cache->isSetUp()) return $cache;

		return $bibsonomy;
	}
}

/*
 * This class communicates with the webservice.
 *
 * Hint: Use the BibSonomyFactory class to produce instances of this class
 * instead of instantiating it yourself.
 */
class BibSonomy {

	private $username, $password;
	private $status;

	public function __construct($username, $password) {
		$this->username = $username;
		$this->password = $password;
		$this->status = new BibSonomyStatus();
	}

	public function createPost($url, $title, $description, array $tags) {
		$post = $this->createXMLPost($url, $title, $description, $tags);
		$this->doCurl(BIBSONOMY_APIURL.'/users/'.$this->username.'/posts/', HTTP_METHOD_POST, $post->getXML());
	}

	public function changePost($url, $title, $description, array $tags, $intraHash) {
		$post = $this->createXMLPost($url, $title, $description, $tags, $intraHash, true);
		$post->setPostingdate();
		$this->doCurl(BIBSONOMY_APIURL.'/users/'.$this->username.'/posts/'.$intraHash, HTTP_METHOD_PUT, $post->getXML());
	}

	# this function should be called by createPost and changePost
	private function checkPost($url, $title, $description, array $tags) {
		# TODO: simply skip invalid urls
		if (empty($url) || substr(strtolower($url), 0, 3) != 'htt') throw new Exception('Invalid URL');
	}

	public function deletePost($intraHash) {
		$this->doCurl(BIBSONOMY_APIURL.'/users/cschenk/posts/'.$intraHash, HTTP_METHOD_DELETE);
	}

	# Hint: The order of the XML elements matters and so does the order of the
	#       method calls in this method.
	private function createXMLPost($url, $title, $description, array $tags, $intraHash = '', $update = false) {
		$post = new XmlPost($this->username, $update);
		$post->setDescription($description);
		$post->setTags($tags);
		$post->setGroup(GROUP_PUBLIC);
		$bookmark = new Bookmark();
		$bookmark->setURL($url);
		$bookmark->setTitle($title);
		$bookmark->setIntraHash($intraHash);
		$bookmark->setInterHash($intraHash);
		$post->setBookmark($bookmark);

		if (DEBUG) echo $post->getXML()."\n";
		return $post;
	}

	private function doCurl($url, $method, $request = NULL) 
	#########################################################
	{
		# FRI - USE ONLY AS GET but OK to HEG proxy
		
		# Superseed AUTH in order to authenticate for bibsonomy
//		global $AUTH_SELF_USERNAME;
//		global $AUTH_SELF_PASSWD;
//		
//		$PRE_AUTH_SELF_USERNAME = $AUTH_SELF_USERNAME;
//		$PRE_AUTH_SELF_PASSWD = $AUTH_SELF_PASSWD;
//		
//		$AUTH_SELF_USERNAME = $this->username;
//		$AUTH_SELF_PASSWD = $this->password;
//		
//		fclose($h);
//		
//		$result = get_file_content($url);
//		
//		$AUTH_SELF_USERNAME = $PRE_AUTH_SELF_USERNAME;
//		$AUTH_SELF_PASSWD = $PRE_AUTH_SELF_PASSWD;		
		
		global $PROXY_NAME, $PROXY_PORT, $PROXY_AUTH_USERNAME, $PROXY_AUTH_PASSWD;
		
		$options = array(
			CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
			CURLOPT_USERPWD => $this->username . ":" . $this->password,
			CURLOPT_HTTPHEADER => array('Accept:text/xml'),
			CURLOPT_TIMEOUT => 10
//			CURLOPT_CONNECTTIMEOUT => 5
		);

		// In case a Proxy has been defined
		if ($PROXY_NAME != '') {
			$options[CURLOPT_PROXYTYPE] = "CURLPROXY_HTTP";
			$options[CURLOPT_PROXY] = "$PROXY_NAME:$PROXY_PORT";
			$options[CURLOPT_PROXYUSERPWD] = "$PROXY_AUTH_USERNAME:$PROXY_AUTH_PASSWD";
		}

		$result = $this->curl_get($url, array(), $options);
		
		if (DEBUG) echo 'Result: '.$result;
		$this->setCurrentStatus($result);
		if (HTTP_METHOD_GET) return $result;
	}

	/**
	 * Utility function making a CURL access to a web service. The parameters
	 * are passed through a GET call.
	 *
	 * @param String $url the base URL of the service.
	 * @param array $get the parameters sent to the service.
	 * @param array $options additional CURL options.
	 */
	private function curl_get($url, array $get = array(), array $options = array()) {
		$defaults = array(
		CURLOPT_HEADER => false,
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_TIMEOUT => 4
		);

		$ch = curl_init($url . (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($get));
		curl_setopt_array($ch, ($options + $defaults));

		if( ! $result = curl_exec($ch)) {
			trigger_error(curl_error($ch));
		}

		curl_close($ch);

		return $result;
	}
	
	
	/*
	 * Sets the current status object for the most recent action.
	 */
	private function setCurrentStatus($result) 
	###########################################
	{
		//$xml = new SimpleXMLElement($result);

		//FRI: here I help reat the stat <bibsonomy stat="ok">
		preg_match('/<bibsonomy stat=\"(\w+)\">/', $result, $matchstat);
		$stat=$matchstat[1];
		
		
		switch ($stat) {
			case 'ok':
				$this->setStatus(STATUS_OK);
				break;
			case 'fail':
				if (DEBUG) echo "Request failed.\nReason: ".$xml->error;
				if (preg_match('/already exists/', $xml->error)) {
					# get hash
					preg_match('/intrahash:[^)]*/', $xml->error, $match);
					$splitMatch = preg_split('/ /', $match[0]);
					$hash = $splitMatch[1];
					$this->setStatus(STATUS_POST_ALREADY_EXISTS, $hash);
				} else {
					$this->setStatus(STATUS_UNKNOWN);
				}
				break;
		}
	}

	/*	
	 * Returns a status object for the last action
	 */
	public function getStatus() {
		return $this->status;
	}

	private function setStatus($code, $description = '') {
		$this->status = new BibSonomyStatus($code, $description);
	}

	/**
	 * Returns a list of public tags for the authorized user.
	 */
	public function getPublicTags($minCount = 10) {
		return $this->getTags('viewable', 'public', $minCount);
	}

	/**
	 * Returns a list of tags for the given user.
	 */
	public function getUserTags($username = NULL, $minCount = 10) {
		return $this->getTags('user', $username, $minCount);
	}

	/*
	 * Returns a list of tags.
	 */
	private function getTags($type = 'user', $name = NULL, $minCount = 10) {
		if ($name == NULL) $name = $this->username;

		$url = BIBSONOMY_APIURL.'/tags?'.$type.'='.$name.'&start=0&end=10000'; # XXX determine sensible value for end or implement an "iterator-fetcher"
		$result = $this->doCurl($url, HTTP_METHOD_GET);
		$xml = new SimpleXMLElement($result);
		$tags = array();
		foreach ($xml->tags->tag as $xmlTag) {
			$tag = new Tag(getAttribute($xmlTag, 'name'));
			$tag->setUserCount(getAttribute($xmlTag, 'usercount'));
			$tag->setGlobalCount(getAttribute($xmlTag, 'globalcount'));
			if ($tag->getUserCount() < $minCount) continue;
			$tags[] = $tag;
		}
		return $tags;
	}


	/*
	 * Returns public posts only.
	 *
	 * XXX: It's not guranteed that it returns the corret amount of posts given by $start and $end.
	 */
	public function getPublicPosts($resourceType, array $tags = NULL, $username = NULL, $start = DEFAULT_POSTS_START, $end = DEFAULT_POSTS_END) {
		$numberOfPosts = $end - $start;
		$rVal = array();
		$posts = $this->getPosts($resourceType, $tags, $username, $start, $end + $this->getOptimisticPrefetch($numberOfPosts));
		foreach ($posts as $post) {
			if ($post->getGroup() != GROUP_PUBLIC) continue;
			$rVal[] = $post;
		}
		return array_slice($rVal, 0, $numberOfPosts);
	}


	public function getAllPublicPosts($resourceType, array $tags = NULL, $username = NULL) {
	/*
	print "<br>getPosts($resourceType, $tags, ($username))<br>";
	print "<br>tags: ";
	var_dump($tags);
	*/
		$xmlposts = $this->getPosts($resourceType, $tags, $username);
		return $xmlposts;
	}







	/*
	 * We're fetching this many posts more than the given $end in the hope that
	 * the result contains enough posts that match our search.
	 *
	 * TODO: implement iterator that fetches the right number of posts.
	 */
	private function getOptimisticPrefetch($numberOfPosts) {
		if ($numberOfPosts < DEFAULT_MAX_POSTS / 2) return DEFAULT_MAX_POSTS;
		return $numberOfPosts * 4;
	}

	/*
	 * Returns posts with the given resource type, tags and user.
	 */
	private function getPosts($resourceType, 
														array $tags = NULL, 
														$username = NULL, 
														$start = DEFAULT_POSTS_START, 
														$end = DEFAULT_POSTS_END) 
	{
	
		global $CALLING_TIMEOUT_SEC; //Aus FRIutilities.php
		global $BIBSONOMY_USER,$BIBSONOMY_APPLICATION_ID;
	
		BibSonomyHelper::assertValidResourceType($resourceType);
		if ($username == NULL) $username = $this->username;
		if ($tags != NULL) {
			$tagUrl = 'tags=';
			foreach ($tags as $tag) $tagUrl .= $tag.'+';
			$tagUrl = substr($tagUrl, 0, strlen($tagUrl) - 1);
		}

		//fri USER MUST BE null IF NOT SET
		if ($user) $USER_QS="&user=".$user;

		$url = BIBSONOMY_APIURL.'/posts/?resourcetype='.$resourceType.$USER_QS.'&start='.$start.'&end='.($start + $end);
		if (isset($tagUrl)) $url .= '&'.$tagUrl;
		//echo "<br>url: $url<br>";

		//print "<br> TRYING TO GET url $url:<br>";
	
		$result = $this->doCurl($url, HTTP_METHOD_GET); // original
		/*FRI:
		$cc= new cURL();	
		print "<br>cc->get_authorized($url,$CALLING_TIMEOUT_SEC,$BIBSONOMY_USER,$BIBSONOMY_APPLICATION_ID);";
		$result = $cc->get_authorized($url,$CALLING_TIMEOUT_SEC,$BIBSONOMY_USER,$BIBSONOMY_APPLICATION_ID);
		
		print "results: ((($result)))";
		*/
		if ($this->getStatus()->getCode() != STATUS_OK) {
			if (DEBUG) echo " STATUS:<b>".$this->getStatus()->getCode()."</b> Couldn't get ".$url;
			//throw new Exception("STATUS NOK:<b>".$this->getStatus()->getCode()."</b>".$resourceType."'");
			$result="<no_results></no_results>"; // Warum solche Fehlermeldungen ... (FRI) lieber ein leeres OBJ
		}

		//print "result: \n((($result)))";
	
		$xml = new SimpleXMLElement($result);
		/* FRI commented
		$posts=array();
		foreach($xml->posts->post as $xmlPost) 
		{
			$post = new PostImpl($username);
			$post->setPostingdate($xmlPost['postingdate']);
			$post->setDescription($xmlPost['description']);
			$post->setGroup($xmlPost['name']);

			// each post-element (tag/name/boockmark)	
			$tags = array();
			foreach ($xmlPost->tag as $tag) {
				$tags[] = array($tag['name'],$tag['href']);
			}
			
			$post->setTags($tags);

			if ($resourceType == 'bookmark') {
				$bookmark = new Bookmark();
				$bookmark->setURL($xmlPost->bookmark['url']);
				$bookmark->setTitle($xmlPost->bookmark['title']);
				$bookmark->setHref($xmlPost->bookmark['href']);
				$bookmark->setIntraHash($xmlPost->bookmark['intrahash']);
				$bookmark->setInterHash($xmlPost->bookmark['interhash']);
				$post->setBookmark($bookmark);
			} else if ($resourceType == 'bibtex') {
				$bibtex = new BibTex();
				$bibtex->setURL($xmlPost->bibtex['url']);
				$bibtex->setTitle($xmlPost->bibtex['title']);
				$bibtex->setHref($xmlPost->bibtex['href']);
				$bibtex->setIntraHash($xmlPost->bibtex['intrahash']);
				$bibtex->setInterHash($xmlPost->bibtex['interhash']);
				$bibtex->setAuthor($xmlPost->bibtex['author']);
				$bibtex->setYear($xmlPost->bibtex['year']);
				$bibtex->setPublisher($xmlPost->bibtex['publisher']);
				$bibtex->setAddress($xmlPost->bibtex['address']);
				$bibtex->setPrivnote($xmlPost->bibtex['privnote']);
				$bibtex->setPages($xmlPost->bibtex['pages']);
				$bibtex->setEntrytype($xmlPost->bibtex['entrytype']);
				$bibtex->setBooktitle($xmlPost->bibtex['booktitle']);
				$bibtex->setBibTexKey($xmlPost->bibtex['bibtexKey']);
				$bibtex->setMisc($xmlPost->bibtex['misc']);
				$post->setBibTex($bibtex);
			
			}
			$posts[] = $post;
		}
		*/

		return $xml;
	}
}

?>
