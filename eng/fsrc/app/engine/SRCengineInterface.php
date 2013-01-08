<?php

/**
 * Interface for SRC engines
 * 
 * @author Fabio Ricci <fabio.fr.ricci@hesge.ch> (May 2011)
 */
interface SRCEngineInterface {
	/**
	 * Checks if the SRC is only by making a simple query. Should return a valid
	 * XML string, formated as one of the following :
	 * - <src_init_response><user>UserID</user></src_init_response>
	 * - <src_init_response><error>Error message</error></src_init_response>
	 * - <src_init_response></src_init_response>
	 * 
	 * @param string $user the user ID
	 * 
	 * @return a XML valid string
	 */
	public function webStart($user);
	
	/**
	 * Calls the refinement using the parameters given. The only possible
	 * actions are "pre" and "preall" when using this method. Should the refinement
	 * be ok, the expected answer should be formatted as follows :
	 * 
	 * <refine>
	 * 	<cid>CID</cid><c></c>
	 * 	<v>Base64 refinement query</v><l>en</l><w>0</w>
	 *  <q/><sid>CID</sid>
	 *  <srv>
	 *  	<pre>Base 64 pre-treatment of query</pre>
	 *  	<pre_raw></pre_raw>
	 *  	<broader>Base 64 broader-terms label</broader>
	 *  	<broader_raw>Base 64 broader-terms URI</broader_raw>
	 *  	<narrower></narrower><narrower_raw></narrower_raw>
	 *  	<related></related><related_raw></related_raw>
	 * 	</srv>
	 * 	<srv_raw/>
	 * 	<maxDur>Timeout time</maxDur>
	 * 	<rts>1255287670</rts>
	 * 	<cdur>3030</cdur>
	 * 	<action>preall</action>
	 * </refine>
	 * 
	 * @param string $sid ?
	 * @param string $q the meta-search query if any
	 * @param string $v the query text to be refined
	 * @param int    $w Widget number
	 * @param string $lang the language of $v
	 * @param int    $m the max number of results needed
	 * @param string $lang the language of $v
	 * @param string $sortrank the control for sorting/rannking results
	 * @param int    $c computation key
	 * @param string $cid ?
	 * @param string $action refine action to perform
	 * @param string $reqClassName - the name of the requested/used/called class 
	 * 
	 * @return a XML valid string
	 */
	public function webRefine($sid, $q, $v, $w, $lang, $m, $sortrank,$maxdur, $c, $cid, $action, $reqClassName);
}

?>
