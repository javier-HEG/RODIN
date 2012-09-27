/*
	Copyright (c) PORTANEO.

	This file is part of COLLABORATION SUITE of POSH http://sourceforge.net/projects/posh/.

	POSH is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version

	POSH is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Posh.  If not, see <http://www.gnu.org/licenses/>.
*/
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Portaneo javascript functions
// é à è ù 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

var excludedKw=new Array;
$p.app.menu.optionSelected=0; // profile tab is selected by default

//reset posh information
$p.app.getVersion=function()
{
	p_version=__PEEVERSION;
};

/*
 *  Class: $p.friends
 *
 *
 *  file: application.js
 *
 */


$p.friends={
	avaiList:[],
	selList:[],
	selMenuItem:1,
	nbGroups:0,
	/*
		$p.friends.items : define a user you can share items with 
		inputs :
			id: user ID
			name : user name
			email : user email
	*/
	item:function(id,name,email)
	{
		this.id=id;
		this.name=name;
		this.email=email;
	},
	/*
		Function: $p.friends.menu 
        
                                display sharing menu
        
		parameters : 
        
			v_type - type of the object shared (2=article, 3=widget, 4=portal,5=??,6=other)
			
                            v_id - id of the object shared
	*/
	menu:function(v_type,v_id,v_extra,datas)
	{
		var l_s = '<br /><div class="sharemenu">'
            + '<div class="shadowunderline">',
            l_title,
            l_allowSendByEmail = true;
		
        var l_url="";
		switch (v_type){
			case 2:
				l_s += '<img src="'+tab[$p.app.tabs.sel].module[$p.app.widgets.uniqToId(tab[$p.app.tabs.sel].RssArticles[v_id].modUniq)].icon+'" width="16" height="16" /> '
                    + '<a href="'+tab[$p.app.tabs.sel].RssArticles[v_id].link+'" target="_blank">'+tab[$p.app.tabs.sel].RssArticles[v_id].title+'</a>';
				l_title = tab[$p.app.tabs.sel].RssArticles[v_id].title;
				break;
			case 3:
				l_s += '<img src="'+tab[$p.app.tabs.sel].module[v_id].icon+'" width="16" height="16" /> '
                    + tab[$p.app.tabs.sel].module[v_id].name;
				l_title = tab[$p.app.tabs.sel].module[v_id].name;
				break;
			case 4:
				l_s += '<img src="'+((tab[$p.app.tabs.sel].icon=='' || tab[$p.app.tabs.sel].icon=='../modules/pictures/_deficon0.gif')?'../images/ico_portal.gif':tab[$p.app.tabs.sel].icon)+'" /> '
                    + tab[$p.app.tabs.sel].label;
				l_title = tab[$p.app.tabs.sel].label;
				break;
            case 5:
                l_title = datas['title'];
                l_url = datas['url'];
                break;
            case 6:
                l_s += v_extra.title;
                l_title = v_extra.title;
                l_allowSendByEmail = false;
		}
        l_s += '</div>'
			+ '<table width="640">'
			+ '<tr>';
            
        if (__useNotebook)
        {
			l_s += '<td valign="top" width="370">'
    			+ $p.html.buildTitle($p.img('ico_notebook.gif',16,16,'','imgmid')+' '+lg("shareOnNotebook"))
    			+ '<div id="sharemenu_tabpublishcontent" style="padding: 6px">'
    			+ '<form name="friend2" method="post" onsubmit="$p.forms.disableAllButton(this);$p.friends.valid('+v_type+','+v_id+',this);return false;">'
    			+ lg('title')+'<br />'
                + '<input type="text" class="thinbox" name="title" id="p_title" maxlength="100" style="width: 300px" value="'+l_title+'" /> '
                + '<input type="hidden" class="thinbox" name="url" id="url" value="'+l_url+'" /> '
                + tooltip('sharedTitleHlp')+'<br />'
                + lg('keywords')+'<br />'
                + '<input type="text" class="thinbox" id="divSharedTags" name="kw" maxlength="100" style="width: 300px" onkeyup=\'$p.tags.autocompletion.get("divSharedTags")\' onblur="$p.tags.autocompletion.hide()" onfocus=\'$p.tags.selectBox.build(this)\' /> '
                + (__restrictOnExistingTags == true ? '' : tooltip('sharedTagHlp'))+'<br />'
    			+ lg('desc')+' '
                + tooltip('SharedDescHlp')+'<br />'
                + '<textarea id="p_desc" name="desc" style="width: 330px;height: 166px;">'
                + (v_extra ? v_extra.description : '')
                + '</textarea>'
    			+ '<table width="300px">'
    			+ '<tr>'
    			+ '<td valign="top" width="100">'
    			+ '<b>'+lg('access')+'</b><br />'
    			+ '<input type="radio" name="access" value="3" checked="checked" />'+lg('public')+' '+tooltip('publicHlp')+'<br />'
    			+ '<input type="radio" name="access" value="2" />'+lg('myNetwork')+'/'+lg('members')+'<br />'
    			+ '<input type="radio" name="access" value="1" />'+lg('private')+'<br />'
    			+ '</td>'
    			+ '<td valign="top">'
    			+ '<div id="divGroupList">'
    			+ '<b>'+lg('Groups')+' '+tooltip('sharedGroupbookHlp')+'</b>'
    			+ '<div id="sharegroups" style="height: 70px;width: 260px;overflow: auto;"></div>'
    			+ '</div>'
    			+ '</td>'
    			+ '</tr>'
    			+ '</table>'
    			+ '<br /><br /><center>'
    			+ '<input type="submit" class="submit" value="'+lg('lblFriendShareBtn'+v_type)+'" />'
    			+ ' <a href="#" onclick="$p.app.popup.hide();return false;">'+lg('cancel')+'</a>'
    			+ '</center>'
    			+ '</form>'
    			+ '</div>'
    			+ '</td>';
        }

        if (l_allowSendByEmail)
        {
            l_s += '<td valign="top" style="border: 1px solid #aaa;padding: 0px 10px 0px 10px;">'
    			+ $p.html.buildTitle($p.img('ico_myaccount.gif',16,16,'','imgmid')+' '+lg("shareWithSomeUsers"))
    			+ '<div id="sharemenu_tabsharecontent" style="padding: 6px">'
    			+ '<form name="friend1" method="post" onsubmit="$p.forms.disableAllButton(this);return $p.friends.valid('+v_type+','+v_id+',this)">'
                + '<input type="hidden" class="thinbox" name="title" id="title" value="'+l_title+'" /> '
                + '<input type="hidden" class="thinbox" name="url" id="url" value="'+l_url+'" /> '
    			+ '<div nowrap="nowrap" style="height: 20px;">'+lg('myNetwork')+'&nbsp;<span id="sharekeywords"></span></div>'
                + '<div id="sharemynetwork" class="cleardiv" style="width:200px;height:120px;overflow:auto;font-size:0.9em;"></div>'
                + lg('outsideMyNetwork')+'<br />'
                + '<input type="text" class="thinbox" name="email" value="'+lg('email')+'" /> '
                + '<input type="button" class="btn" value="'+lg('add')+'" onclick="$p.friends.addEmail();" /><br /><br />'
    			+ '<div nowrap="nowrap" style="height: 20px;">'+lg('selectedPeople')+' :</div><div id="sharepeopleselected" class="cleardiv" style="width:200px;height:100px;overflow:auto;font-size:0.9em;"></div><br /><br />'
    			+ '<input type="submit" class="submit" value="'+lg('lblFriendShareBtn'+v_type)+'" />'
    			+ '</form>'
    			+ '</div>'
    			+ '</td>'
        }
		l_s	+= '</tr>'
			+ '</table>';

        l_s += "</div>";   

		if( v_type != 5 ) {
			$p.app.popup.show(l_s,660,indef,$p.img("ico_menu_share.gif",16,16,"","imgmid")+" "+lg("lblFriendShareBtn"+v_type));
		}
		navWait("sharemynetwork");
		$p.friends.loadKeywords();
		$p.friends.loadFriends(0);
		navWait("sharegroups");
		$p.friends.loadGroups("divGroupList");
		//initialize lists
		$p.friends.selList.length=0;
		$p.friends.avaiList.length=0;
		
		
		return l_s;
	},
	/*
                      Function : displayItem
                            $p.friends.displayItem : display / hide sharing sub menus
		inputs :
                            v_id : friend ID
		
	*/
	displayItem:function(v_id)
	{
		$p.friends.selMenuItem=v_id;
		if (v_id==1)
		{
			navShow("sharediv1","block");
		}
		else
		{
			navShow("sharediv1","none");
		}
		if (v_id==3)
		{
			navShow("sharediv3","block");
		}
		else
		{
			navShow("sharediv3","none");
		}
	},
	/*
		$p.friends.loadKeywords : load keywords linked to my network
	*/
	loadKeywords:function()
	{
		$p.ajax.call(pep["xmlnetwork_keywords"],
			{
				'type':'load',
				'callback':
				{
					'function':$p.friends.showKeywords
				}
			}
		);
	},
	/*
		$p.friends.showKeywords : display keywords linked to my network
		inputs : xml return		
	*/
	showKeywords:function(response,vars)
	{
		var i=0,l_s="",l_result=response.getElementsByTagName("keyword");
		l_s+=" <select name='keywords' onchange='$p.friends.loadFriends(this.value)'><option value='0'>"+lg("all")+"</option>";
		for (var i=0;i<l_result.length;i++)
		{
			l_id=$p.ajax.getVal(l_result[i],"id","int",false,0);
			l_s+="<option value='"+l_id+"'>"+$p.ajax.getVal(l_result[i],"label","str",false,"---")+"</option>";
		}
		l_s+="</select>";
		$p.print("sharekeywords",l_s);
	},
	/*
		Function : loadFriends
                            $p.friends.loadFriends : load network list
		Parameters :
                             keyword ID (if filtered by keyword)
	*/
	loadFriends: function(v_kwid)
	{
		$p.ajax.call(pep["xmlnetwork_users"]+"?kwid="+v_kwid+'&s=0',
			{
				'type':'load',
				'callback':
				{
					'function':$p.friends.showFriends
				}
			}
		);
	},
	/*
		Function : showFriends
                            $p.friends.showFriends : display network list
		Parameters :
                            xml return
	*/
	showFriends: function(response,vars)
	{
		var l_s = "",
            l_result = response.getElementsByTagName("user");

		$p.friends.avaiList.length = 0;
		if (l_result.length == 0)
		{
			l_s = '<a href="#" onclick=\'$p.app.popup.hide();$p.network.dashboard.myNetwork();\'>'+lg("addFriend")+'</a>';
		}
		else
		{
			if (l_result.length > 1)
                l_s += "<a href='#' onclick='$p.friends.addAllFriends()'>"+lg("addAllFriends")+"</a><br />";

			for (var i = 0;i < l_result.length;i ++)
			{
				var l_id = $p.ajax.getVal(l_result[i],"id","int",false,0),
                    l_name = $p.ajax.getVal(l_result[i],"longname","str",false,"?"),
                    l_email = $p.ajax.getVal(l_result[i],"username","str",false,"");

                $p.friends.avaiList.push(new $p.friends.item(l_id,l_name,l_email));
				l_s += "<a href='#' onclick=\"$p.friends.addFriend("+i+")\">"
                    + l_name
                    + " "+$p.img("ico_add.gif",7,7,lg("add"),"imgmid")
                    + "</a>"
                    + "<br />";
			}
		}
		$p.print("sharemynetwork",l_s);
	},
	/*
		$p.friends.addAllFriends : add all my network in shared list
	*/
	addAllFriends:function()
	{
		for (var i=0;i<$p.friends.avaiList.length;i++)
		{
			$p.friends.addFriend(i,false);
		}
		$p.friends.showSelected();
	},
	/*
		Function : addFriend
                            $p.friends.addFriend : add a network item in the shared list
		Parameters :
			v_id : user ID in the network list
			v_refresh : refresh network list after process
	*/
	addFriend:function(v_id,v_refresh)
	{
		var l_newItem = new $p.friends.item($p.friends.avaiList[v_id].id,
                                            $p.friends.avaiList[v_id].name,
                                            $p.friends.avaiList[v_id].email);
		//check if the friend does not exist in the list
		for (var i = 0;i < $p.friends.selList.length;i ++)
		{
			if ($p.friends.selList[i].id == l_newItem.id) return;
		}
		$p.friends.selList.push(l_newItem);
		
		if (v_refresh == indef || v_refresh)
            $p.friends.showSelected();
	},
	/*
		Function : supFriend
                            $p.friends.supFriend : suppress network item from shared list
		Parameters :
                            v_id : user ID in the shared list
	*/
	supFriend: function(v_id)
	{
		$p.friends.selList.splice(v_id,1);
		$p.friends.showSelected();
	},
    /*
                    Function : addEmail
                        $p.friends.addEmail : add email in the sendee list for sharing
           */
    addEmail: function()
    {
        var l_email = document.forms['friend1'].email.value;
        if (checkEmail(l_email))
        {
            var l_newItem = new $p.friends.item(0,
                                                l_email,
                                                l_email);
            $p.friends.selList.push(l_newItem);
            $p.friends.showSelected();
        }
    },
	/*
		Function : showSelected
                            $p.friends.showSelected : refresh the shared list
	*/
	showSelected: function()
	{
		var l_s = "";
		for (var i = 0;i < $p.friends.selList.length;i ++)
		{
			l_s += $p.friends.selList[i].name
                + " <a href='#' onclick='$p.friends.supFriend("+i+")'>"
                + $p.img("ico_suppress.gif",7,7)
                + "</a>"
                + "<br />";
		}
		$p.print("sharepeopleselected",l_s);
	},
	/*
		Function : valid
                            $p.friends.valid : confirm sharing & launch sharing processes
		Parameters :
			v_type : type of the item shared (2=article, 3=widget, 4=portal)
			v_id : id of the item shared
	*/
	valid: function(v_type,v_id,v_form)
	{
		// if article, get the array feed id
		//if (v_type==2) v_id=$p.app.widgets.rss.getId(v_id);

		//shared item information
//		var l_objInfo="";
//		if (v_type==3){l_objInfo+="&obj=m&id="+tab[$p.app.tabs.sel].module[v_id].id+"&v="+$p.string.esc(tab[$p.app.tabs.sel].module[v_id].vars);}
//		else if (v_type==4) {l_objInfo+="&obj=p&prof="+v_id+"&label="+$p.string.esc(tab[$p.app.tabs.sel].label)+"&nbcol="+tab[$p.app.tabs.sel].colnb+"&style="+tab[$p.app.tabs.sel].style+"&mode="+tab[$p.app.tabs.sel].showType;}
//		else if (v_type==2) {l_objInfo+="&obj=a&title="+$p.string.esc($p.article.format(tab[$p.app.tabs.sel].feeds[v_id].title))+"&link="+$p.string.esc(correctCharEncoding(tab[$p.app.tabs.sel].feeds[v_id].link));}
//		if (v_type==4 && l_form.notebook.checked) l_objInfo+="&portname="+l_form.title.value+"&portdesc="+(l_form.desc.value).substr(0,200)+"&kw="+l_form.kw.value;

		if (v_form.name == "friend1")
		{
			if ($p.friends.selList.length > 0)
			{
				switch (v_type)
				{
					case 2:
						$p.friends.shareNews($p.string.esc($p.article.format(tab[$p.app.tabs.sel].RssArticles[v_id].title)),$p.string.esc($p.string.correctEncoding(tab[$p.app.tabs.sel].RssArticles[v_id].link)));
						break;
					case 3:
						$p.friends.shareWidget(tab[$p.app.tabs.sel].module[v_id].uniq,$p.app.tabs.sel);
						break;
					case 4:
						$p.friends.sharePortal(v_id,$p.string.esc(tab[$p.app.tabs.sel].label));
						break;
                    case 5:
                        var l_title = $p.string.esc(v_form.title.value);
                        var urlModFF = v_form.url.value;
                        $p.friends.shareNews(l_title,urlModFF);
                        break;
				}
			}   
		}
		else
		{
			var l_title = $p.string.removeTags(v_form.title.value),
                l_desc = v_form.desc.value,
                l_kw = $p.tags.formatList(v_form.kw.value);
			switch (v_type)
			{
				case 2:
					var v_tGroup = $p.group.getSelected(v_form);
                    l_desc = $p.string.textToHtml($p.string.removeTags(l_desc));
					$p.friends.publishNews(v_id,$p.app.tabs.sel,l_title,l_desc,l_kw,$p.app.tools.getRadioValue(v_form.access),v_tGroup);
					break;
				case 3:
                    l_desc = $p.string.textToHtml($p.string.removeTags(l_desc));
					$p.friends.publishWidget(v_id,$p.app.tabs.sel,l_title,l_desc,l_kw,$p.app.tools.getRadioValue(v_form.access));
					break;
				case 4:
                    l_desc = $p.string.textToHtml($p.string.removeTags(l_desc));
					$p.friends.publishPortal(v_id,l_title==""?tab[$p.app.tabs.sel].label:l_title,l_desc,l_kw,$p.app.tools.getRadioValue(v_form.access));
					break;
				case 5:
					var urlModFF = v_form.url.value;
					$p.friends.publishNews(v_id,"",l_title==""?tab[$p.app.tabs.sel].label:l_title,l_desc,l_kw,"3","",8,urlModFF);
					break;
                case 6:
                    $p.friends.publishItem(l_title,l_desc,l_kw,$p.app.tools.getRadioValue(v_form.access));
                    break;
			}
		}
		//$p.app.menu.hide();
		$p.app.popup.hide();

		return false;
	},
	/*
		$p.friends.shareNews : share a news with another user
		inputs : 
			v_title : news title
			v_link : news link
	*/
	shareNews:function(v_title,v_link)
	{
		var l_title = $p.app.user.name
                    +lg("invitesYouOnArticle")+__APPNAME,
            l_desc = lg("hello")+",\r\n\r\n "+$p.app.user.name
                   +lg("invitesYouOnArticleBody",$p.string.unesc(v_title))
                   +lg("invitesYouOnArticleBody2",$p.string.unesc(v_link))
                   +__APPNAME+".";

		notifyByEmail($p.friends.emailsArray(),l_title,l_desc,indef);
	},
	/*
		$p.friends.shareWidget : share a widget with another user
		inputs : 
			v_id : widget unique id
	*/
	shareWidget:function(v_id,v_tab)
	{
		var l_securedString=$p.string.randomize(15);
		//save widget information
		$p.ajax.call(pep["scr_shareitem"],
			{
				'type':'load',
				'source':'xml',
				'method':'POST',
				'callback':
				{
					'function':$p.friends.sendWidgetNotification,
					'variables':
					{
						'id':v_id,
						'tab':v_tab,
						'chk':l_securedString
					}
				},
				'variables':'obj=m&uniq='+v_id+'&prof='+tab[v_tab].id+'&secured='+l_securedString
			}
		);
	},
	sendWidgetNotification:function(response,vars)
	{
		//var l_chk=$p.ajax.getVal(response,"widgetchk","str",false,"");
        var l_chk=$p.ajax.getVal(response,"secured","str",false,"");
		var l_link=__LOCALFOLDER+"portal/"+pep["addtoapplication"]+"?id="+vars['id']+"&tab="+tab[vars['tab']].id+"&chk="+l_chk;
		var l_subject=$p.app.user.name+lg("invitesYouOn")+__APPNAME;
		var l_body=lg("invitesYouOnBody",$p.app.user.name)+lg("invitesYouOnBody2",l_link)+__APPNAME+".";

		notifyByEmail($p.friends.emailsArray(),l_subject,l_body);
	},
	/*
		$p.friends.sharePortal : share a portal with another user
		Parameters : 
			v_id - portal id
			v_name - portal name
			v_tags - tags relative to the shared page
	*/
	sharePortal:function(v_id,v_name,v_tags)
	{
		var l_securedString=$p.string.randomize(15);
		//save portal information
		$p.ajax.call(pep["scr_shareitem"],
			{
				'type':'load',
				'source':'xml',
				'method':'POST',
				'callback':
				{
					'function':$p.friends.sendPortalNotification,
					'variables':
					{
						'id':v_id,
						'chk':l_securedString
					}
				},
				'variables':'obj=p&prof='+v_id+'&label='+v_name+'&secured='+l_securedString+'&kw='+v_tags
			}
		);
	},
	sendPortalNotification:function(response,vars)
	{
		//var l_chk=$p.ajax.getVal(response,"portalchk","str",false,"");
		//var l_portalid=$p.ajax.getVal(response,"portalid","str",false,0);
		var l_subject=$p.app.user.name+lg("invitesYouOn")+__APPNAME;
		var l_body=lg("invitesYouOnPortalBody",$p.app.user.name)+lg("invitesYouOnPortalBody2",__LOCALFOLDER+"/portal/"+pep["addportaltoapplication"]+"?id="+vars['id']+"&chk="+vars['chk'])+__APPNAME+".";

		notifyByEmail($p.friends.emailsArray(),l_subject,l_body);

        //show that page is shared
        tab[$p.app.tabs.sel].shared = 1;
        $p.app.tabs.refresh($p.app.tabs.sel);
	},

	/*
		$p.friends.publishNews : publish a news
		inputs : 
			v_articleId : ID of the article selected
			v_tab : tab the article is displayed on
			v_title : title of the article
			v_desc : description of the article
			v_keywords : keywords of the article (coma separated)
			v_access : access to the article (3=public 2=my network 1=private)
			v_tGroup : array of groups in which to share article
	*/
	publishNews: function(v_articleId,v_tab,v_title,v_desc,v_keywords,v_access,v_tGroup,type,url)
	{
		var idTmp = "";
		var typeTmp = "2";
		if( type != 8 ) {
			var l_desc = v_desc
                + "<br /><br /><div class=notebooklink>"
                + "<a href='"+tab[v_tab].RssArticles[v_articleId].link+"' target='_blank'>"
                + "<img src='"+tab[v_tab].module[$p.app.widgets.uniqToId(tab[v_tab].RssArticles[v_articleId].modUniq)].icon+"' width='16' align='absmiddle' /> "
                + $p.string.removeTags($p.article.format(tab[v_tab].RssArticles[v_articleId].title))+"</a>"
                + "<br />"+tab[v_tab].module[$p.app.widgets.uniqToId(tab[v_tab].RssArticles[v_articleId].modUniq)].name+" ("+$p.date.format(tab[v_tab].RssArticles[v_articleId].date)+")</div>";
			var idTmp = tab[v_tab].RssArticles[v_articleId].id;
		} else {
			var l_desc = v_desc+"<br /><br /><div class=notebooklink>"
				+ "<a href='"+url+"' target='_blank'>"
				+ "<img src='"+"icon.ico"+"' width='16' align='absmiddle' /> "
	            + v_title+"</a>"
				+ "<br />"+v_title+"</div>";
				var typeTmp = type;
		}
		if (v_title == "" || v_title == indef) v_title = $p.article.format($p.string.removeTags(tab[v_tab].RssArticles[v_articleId].title));
		//add article to notebook					
		$p.notebook.addArticle(v_title,l_desc,v_keywords,typeTmp,idTmp,idTmp,v_access,v_articleId, v_tGroup);
		//$p.notebook.addArticle(v_title,l_desc,v_keywords,"2",tab[v_tab].RssArticles[v_articleId].id,tab[v_tab].RssArticles[v_articleId].id,v_access,v_articleId, v_tGroup);
		
	},
    publishItem: function(v_title,v_desc,v_keywords,v_access,v_tGroup)
    {
        $p.notebook.addArticle(v_title,v_desc,v_keywords,9,'',2,v_access,0, v_tGroup);
    },
	/*
		$p.friends.publishWidget : publish a widget
		inputs : 
			v_widgetId : ID of the widget selected
			v_tab : tab the widget is displayed on
			v_title : title of the widget
			v_desc : description of the widget
			v_keywords : keywords of the widget (coma separated)
			v_access : access to the widget (3=public 2=my network 1=private)
	*/
	publishWidget:function(v_widgetId,v_tab,v_title,v_desc,v_keywords,v_access,v_tGroup)
	{
		//share widget
		var l_vars=tab[v_tab].module[v_widgetId].vars+'&shared=1';

		$p.ajax.call(pep["scr_shareitem"],
			{
				'type':'load',
				'source':'xml',
				'method':'POST',
				'callback':
				{
					'function':$p.friends.sendNotification
				},
				'variables':'obj=m&uniq='+tab[v_tab].module[v_widgetId].uniq+'&prof='+tab[v_tab].id+'&secured='+v_access
			}
		);

		//add article to notebook
		var l_desc = v_desc
            + "<br /><br />"
            + "<a href='#' act=\"$p.notebook.addWidget("+tab[v_tab].module[v_widgetId].id+",'"+l_vars+"');return false;\"><img src='../images/ico_menu_add.gif' /> "+lg("addThisModuleInMyPage")+" ("+tab[v_tab].module[v_widgetId].name+")</a>";
            //+ "<a href='#' onclick=\"$p.notebook.addWidget("+tab[v_tab].module[v_widgetId].id+",'"+l_vars+"');return false;\"><img src='../images/ico_menu_add.gif' /> "+lg("addThisModuleInMyPage")+" ("+tab[v_tab].module[v_widgetId].name+")</a>";

		if (v_title=="" || v_title==indef) v_title=lg("newModule");

		$p.notebook.addArticle(v_title,l_desc,v_keywords,"3",0,tab[v_tab].module[v_widgetId].id,v_access,v_widgetId);
	},
	/*
		$p.friends.publishPortal : publish a portal
		inputs : 
			v_tab : ID of the tab selected
			v_title : title of the portal
			v_desc : description of the portal
			v_keywords : keywords of the portal (coma separated)
			v_access : access to the portal (3=public 2=my network 1=private)
	*/
	publishPortal:function(v_tab,v_title,v_desc,v_keywords,v_access)
	{
		//share portal
		$p.ajax.call(pep["scr_shareitem"],
			{
				'type':'load',
				'source':'xml',
				'method':'POST',
				'callback':
				{
					'function':$p.friends.publishPortalAddArticle,
					'variables':
					{
						'title':v_title,
						'desc':v_desc,
						'keywords':v_keywords,
						'tab':v_tab,
						'access':v_access
					}
				},
				'variables':'obj=p&prof='+v_tab+'&secured='+v_access+'&kw='+v_keywords+'&portname='+$p.string.esc(v_title)
			}
		);
	},
	publishPortalAddArticle:function(response,vars)
	{
		//var l_portalId=$p.ajax.getVal(response,"portalid","int",false,0);
		//add article to notebook
		$p.notebook.addArticle(vars['title'],vars['desc'],vars['keywords'],"4",0,vars['tab'],vars['access'],vars['tab']);

        //show that page is shared
        tab[$p.app.tabs.sel].shared = 1;
        $p.app.tabs.refresh($p.app.tabs.sel);
	},
	/*
		$p.friends.sendNotification : send notification after sharing
		inputs :
			v_emails : object type (m=widget, p=portal)
			v_subject : notification subject
			v_message : notification message
	*/
	sendNotification:function(response,vars)
	{
		var l_subject=$p.ajax.getVal(response,"subject","str",false,"");
		var l_body=$p.ajax.getVal(response,"body","str",false,"");

		notifyByEmail($p.friends.emailsArray(),l_subject,l_body,indef);
	},
	/*
		$p.friends.emailsArray : list of selected emails
	*/
	emailsArray:function()
	{
		if ($p.friends.selList.length==0) return false;
		var l_email=new Array();
		for (var i=0;i<$p.friends.selList.length;i++)
		{
			l_email[i]=$p.friends.selList[i].email;
		}
		return l_email;
	},
	/*
		$p.friends.addPortal : add shared portail in my own portal
		inputs :
	*/
	addPortal:function(v_sess){
		if (v_sess){
			var l_form=document.forms["f"];
			$p.app.pages.loadSharedPortal(l_form.id.value,2,l_form.check.value,false,true);
		}
	},
	/*
		$p.friends.loadGroups : load group list for user passed in parameter
		inputs :
		* displayTagId : to display if group list exists
		* user ID :  
	*/
	loadGroups:function(displayTagId, userId)
	{
        if (userId == indef)
        {
			var param;
			if (displayTagId == indef)
            {
				param = "";
			}
            else
			{
				param = displayTagId;
			}
			
			$p.ajax.call(pep["xmlnetwork_userworkinggroups"]+"?okOnly=1",
				{
					'type':'load',
					'callback':
					{
						'function':$p.friends.showGroups,
						'variables':
						{
							'tagid':param
						}
					}
				}
			);
		}
        else
        {
			/*load group list in which userId is not invited or not member*/
			$p.ajax.call(pep["xmlnetwork_userworkinggroups"]+"?uId="+userId,
				{
					'type':'load',
					'callback':
					{
						'function':$p.friends.showGroups,
						'variables':
						{
							'tagid':displayTagId
						}
					}
				}
			);
		}
		
	},
	/*
		$p.friends.showGroups : display group list
		inputs : xml return
	*/
	showGroups: function(response,vars)
	{
		var l_s = "",
            l_result=response.getElementsByTagName("workinggroup");
		
		if (l_result.length == 0)
        {
            l_s = '<i>'+lg('noGroup')+'<i><br /><br />';
        }
        else
		{
			for (var i=0;i<l_result.length;i++)
			{
				l_id=$p.ajax.getVal(l_result[i],"id","int",false,0);
				l_name=$p.ajax.getVal(l_result[i],"name","str",false,"?");
				l_status=$p.ajax.getVal(l_result[i],"status","int",false,0);

				if(l_result.length == 1)
					l_s+="<input type='checkbox' id='"+l_id+"' name='group'\">"+l_name+"<br />";
				else
					l_s+="<input type='checkbox' id='"+l_id+"' name='group[]'\">"+l_name+"<br />";
			}
		}
		$p.print("sharegroups",l_s);

		if (indef != vars['tagid']) {
			if ("" == l_s) {
				navShow(vars['tagid'],'none');
			}
		}
	}
}

// Manage the users' messages
function articleObj(id,title,link,status,source,icon,date,feedArticleId,uniqFeedId,v_tab,article_rating){
	this.id=id;
	this.title=title;
	this.link=link;
	this.status=status;
	this.source=source;
	this.icon=icon;
	this.date=date;
	this.feedArticleId=feedArticleId;
	this.uniqFeedId=uniqFeedId;
	this.v_tab=v_tab;
	this.article_rating=article_rating;
};

/*
 *
 *  Class : $p.article
 *
 *      manage articles
 *
 */


$p.article={
	shown:false,
	items:[],
	contentDiv:'',
	initMenu:function()
	{
        $p.app.menu.options.push({
                                "id":"particles",
                                "label":lg("archives"),
                                "desc":lg("archives"),
                                "icon":"ico_disk.gif",
                                "seq":55,
								"action":"$p.article.menu()",
                                "type":"",
                                "pages":[]
                                }
        );
//		if ($p.app.user.id>0) $p.app.menu.options[1].subOpt.push({"id":"parttoread","label":"lblArchive","seq":0,"action":"$p.article.menu()","pages":new Array()});
	},
	menu:function()
	{
		//$p.app.menu.addTitle('articlemenu_1','myinfo.gif',lg("lblArchive"),$p.article.getUnread);
		$p.app.menu.addHTML('articlemenu_1',"<div id='articlestoread'></div>");

        $p.app.wait('articlestoread');

		//$p.app.menu.openSubMenu('articlemenu_1',true);
        $p.article.getUnread();
		
		if (__useRating)
		{
			$p.app.menu.addHTML('articlemenu_2',"<div id='library'></div>");
			$p.article.getRated();
		}	
	},
	/*
		$p.article.init : init statistics pages
	*/
	init:function()
	{
		$p.app.newEnv('archive');
		$p.app.tabs.openTempTab(3,"$p.plugin.openInTab(%tabid%,function(){},'myarticles')",lg('lblArchive'),'../images/myinfo.gif');
		$p.article.contentDiv='modules'+tab[$p.app.tabs.sel].id;
	},
	/*
		$p.article.load : initialize "articles to read" plugin
		inputs : v_start : list of article starts at number v_start
	*/
	load:function(v_start)
	{
		$p.article.init();
		$p.app.setState("$p.article.load("+v_start+")");
		$p.article.classify.redirect=true;

		$p.app.wait($p.article.contentDiv);

		if (v_start==indef) v_start=0;

		$p.ajax.call(pep["xmlarticles"]+"?start="+v_start+"&nb=10",
			{
				'type':'load',
				'callback':
				{
					'function':$p.article.get
				}
			}
		);
	},
	/*
		$p.article.get : register articles
		inputs : xml response
	*/
	get:function(response,vars)
	{
		var i=0;
		$p.article.items.length=0;
		
		while (response.getElementsByTagName("article")[i])
		{
			var l_result=response.getElementsByTagName("article")[i];
			$p.article.items.push(
                new articleObj($p.ajax.getVal(l_result,"id","int",false,0),
                                $p.ajax.getVal(l_result,"title","str",false,"=="),
                                $p.ajax.getVal(l_result,"link","str",false,""),
                                indef,
                                $p.ajax.getVal(l_result,"source","str",false,""),
                                $p.ajax.getVal(l_result,"icon","str",false,""),
                                $p.ajax.getVal(l_result,"pubdate","str",false,""),
                                $p.ajax.getVal(l_result,"feedarticle_id","int",false,0),
								indef,
								indef,
								indef
                )
            );
			i++;
		}
		
		$p.article.display();
	},
	/*
		$p.article.display : refresh "articles to read" list
	*/
	display:function()
	{
		var l_s=$p.html.buildTitle(lg("articlesToRead"));
		var l_bg="clear";
		
		if ($p.article.items.length>0)
		{
			l_s+="<table width='100%' class='ftable' cellspacing='0'>";
			for (var i=0;i<$p.article.items.length;i++)
			{
				if (($p.article.items[i].title).length>98) $p.article.items[i].title+=" ...";
				l_s+="<tr class='"+l_bg+"'>"
					+"<td width='20'>"
					+$p.img($p.article.items[i].icon,16,16)
					+"</td>"
					+"<td>"
					+"<a href='"+$p.article.items[i].link+"' target=_blank><b>"+$p.article.items[i].title+"</b></a>"
					+"<br />"
					+$p.article.items[i].source+", "+lg("onThe")+" "+$p.date.formatDateShort($p.date.convertFromDb($p.article.items[i].date))+"<br />"
					+"<a href='"+$p.article.items[i].link+"' target=_blank>"+lg("openLinkedArticle")+"</a> - "
					+(__useNotebook ? "<a href=# onclick='return $p.article.classify.newDocument("+i+")'>"+$p.img("ico_classify.gif",15,15,lg("archive"),"imgmid")+" "+lg("archive")+"</a> - " : "")
					+"<a href=# onclick='return $p.article.suppress("+i+","+$p.article.items[i].id+")'>"+$p.img("ico_suppress.gif",14,14,lg("suppress"),"imgmid")+" "+lg("suppress")+"</a>"
					+"<br /><br />"
				var source = "archives";
				$p.app.widgets.rss.loadRating($p.article.items[i].feedArticleId,$p.article.items[i].uniqFeedId,'indef',source); 
				$p.app.widgets.rss.loadAverageRating($p.article.items[i].feedArticleId,$p.article.items[i].uniqFeedId,'indef',source);
				l_s += '<div id="user_rating_archives_'+$p.article.items[i].uniqFeedId+'_'+$p.article.items[i].feedArticleId+'"></div>'
					+'<div id="average_rating_archives_'+$p.article.items[i].uniqFeedId+'_'+$p.article.items[i].feedArticleId+'"></div>'
					+"</td>"
					+"</tr>";
				l_bg=(l_bg=="clear" ? "dark" : "clear");
			}
			l_s+="</table>";
		}
		else
		{
			l_s+=lg("lblArchEmpty");
		}
		$p.print($p.article.contentDiv,l_s);
	},
	//ancre2
	/*
		Function: $p.article.loadLibrary 
			initialize "rated articles" plugin
	
		Parameters:
		
			v_star - list of displayed articles starts at number v_start

	*/
	loadLibrary:function(v_start)
	{
		$p.article.init();
		$p.app.setState("$p.article.loadLibrary("+v_start+")");
		$p.article.classify.redirect=true;

		$p.app.wait($p.article.contentDiv);

		if (v_start==indef) v_start=0;

		$p.ajax.call(pep["xml_display_library"]+"?start="+v_start+"&nb=10",
			{
				'type':'load',
				'callback':
				{
					'function':$p.article.getLibrary
				}
			}
		);
	},
	/*
		Function: $p.article.getLibrary 
			get "rated articles" plugin
	
		Parameters:
		
			response - XML object
			vars (array) - variables (optionnal)

	*/
	getLibrary:function(response,vars)
	{ 
		var i=0;
		$p.article.items.length=0;
		
		while (response.getElementsByTagName("article")[i])
		{
			var l_result=response.getElementsByTagName("article")[i];
			$p.article.items.push(
                new articleObj(	indef,
                                $p.ajax.getVal(l_result,"article_title","str",false,""),
                                $p.ajax.getVal(l_result,"link","str",false,""),
								indef,
								$p.ajax.getVal(l_result,"feed_title","str",false,""),
								$p.ajax.getVal(l_result,"icon","str",false,""),
								$p.ajax.getVal(l_result,"rating_timestamp","str",false,"0"),
								$p.ajax.getVal(l_result,"article_id","int",false,""),
								$p.ajax.getVal(l_result,"uniq","int",false,""),
								$p.ajax.getVal(l_result,"seq","int",false,""),
                                $p.ajax.getVal(l_result,"article_rating","int",false,"")
                )
            ); 
			i++;
		}
		$p.article.displayLibrary();
	},
	/*
		Function: $p.article.displayLibrary 
			display the library of rated articles
	*/	
	displayLibrary:function()
	{	
	var l_s=$p.html.buildTitle(lg("articlesRated"));
		var l_bg="clear";

		if ($p.article.items.length>0)
		{ 
			l_s+="<table width='100%' class='ftable' cellspacing='0'>";
			for (var i=0;i<$p.article.items.length;i++)
			{ 
				if (($p.article.items[i].title).length>98) $p.article.items[i].title+=" ...";
				l_s+="<tr class='"+l_bg+"'>"
					+"<td width='20'>"
					+$p.img($p.article.items[i].icon,16,16)
					+"</td>"
					+"<td>"
					+"<a href='"+$p.article.items[i].link+"' target=_blank><b>"+$p.article.items[i].title+"</b></a>"
					+"<br />"
					+$p.article.items[i].source+", "+lg("onThe")+" "+$p.article.items[i].date+/*$p.date.formatDateShort($p.date.convertFromDb($p.article.items[i].date))+*/"<br />"
					+"<a href='"+$p.article.items[i].link+"' target=_blank>"+lg("openLinkedArticle")+"</a> - "
					+(__useNotebook ? "<a href=# onclick='return $p.article.classify.newDocument("+i+")'>"+$p.img("ico_classify.gif",15,15,lg("archive"),"imgmid")+" "+lg("archive")+"</a> - " : "")
					+"<a href=# onclick='return $p.article.suppress("+i+","+$p.article.items[i].id+")'>"+$p.img("ico_suppress.gif",14,14,lg("suppress"),"imgmid")+" "+lg("suppress")+"</a>"
					+"<br /><br />"
					
				var source = "archives";
				$p.app.widgets.rss.loadRating($p.article.items[i].feedArticleId,$p.article.items[i].uniqFeedId,'indef',source); 
				$p.app.widgets.rss.loadAverageRating($p.article.items[i].feedArticleId,$p.article.items[i].uniqFeedId,'indef',source);
				l_s += '<div id="user_rating_archives_'+$p.article.items[i].uniqFeedId+'_'+$p.article.items[i].feedArticleId+'"></div>'
					+'<div id="average_rating_archives_'+$p.article.items[i].uniqFeedId+'_'+$p.article.items[i].feedArticleId+'"></div>'
					+"</td>"
					+"</tr>";
				l_bg=(l_bg=="clear" ? "dark" : "clear");
			}
			l_s+="</table>";
		}
		else
		{
			l_s+=lg("lblArchEmpty");
		}
		$p.print($p.article.contentDiv,l_s);
	}, 
	/*
		$p.article.format : format the articles for display
		inputs : article
	*/
	format:function(v_s)
	{
		return v_s.substr(0,99);
	},
	/*
		$p.article.save : save an article from a personalized page
		inputs : article ID
	*/
	save:function(v_id)
	{
		//var l_feedid=$p.app.widgets.rss.getId(v_id);
		var l_source=$p.string.esc($p.article.format(tab[$p.app.tabs.sel].RssArticles[v_id].modName));
		var l_title=$p.string.esc($p.article.format(tab[$p.app.tabs.sel].RssArticles[v_id].title));
		var l_link=$p.string.esc(tab[$p.app.tabs.sel].RssArticles[v_id].link);
		var l_id=$p.app.widgets.uniqToId(tab[$p.app.tabs.sel].RssArticles[v_id].modUniq);
		var l_icon=tab[$p.app.tabs.sel].module[l_id].icon;
		var l_date=$p.date.getDbFormat((tab[$p.app.tabs.sel].RssArticles[v_id].date).getDate(),
                                        (tab[$p.app.tabs.sel].RssArticles[v_id].date).getMonth(),
                                        (tab[$p.app.tabs.sel].RssArticles[v_id].date).getFullYear());
		var l_id=tab[$p.app.tabs.sel].RssArticles[v_id].id;
		
		$p.ajax.call(pep["scr_savearticle"],
			{
				'type':'execute',
				'variables':'t='+l_title+'&l='+l_link+'&s='+l_source+'&i='+l_icon+'&d='+l_date+'&id='+l_id
			}
		);

		$p.app.alert.show(lg("msgSrcSave"));

		return false;
	},
	/*
		$p.article.hide : close article plugin
	*/ 
	hide:function()
	{
		$p.plugin.hide();
	},
	/*
		$p.article.suppress : suppress an article from the list
		inputs :
			v_id : article ID (of the javascript list)
			v_dbid : article ID (in the DB)
	*/
	suppress:function(v_id,v_dbid)
	{
		var l_input=confirm(lg("msgArchSup"));
		if (l_input==1)
		{
			$p.article.items.splice(v_id,1);

			$p.ajax.call(pep["scr_suparticle"],
				{
					'type':'execute',
					'variables':'id='+v_dbid
				}
			);
			
			$p.article.display();
		}
	},
	/*
		$p.article.getUnread : get number of unread articles
	*/
	getUnread:function()
	{
		if ($p.app.user.id==0)	
			$('articlestoread').set('html',lg('msgNeedToBeConnectedMenu'));
		else
		{
			$p.ajax.call(pep["xmlnbarticles"],
				{
					'type':'load',
					'callback':
					{
						'function':$p.article.displayUnread
					}
				}
			);
		}
	},
	/*
		$p.article.displayUnread: display number of unread articles
		inputs : xml response
	*/
	displayUnread:function(response,vars)
	{
		var l_s='';
		var l_nbarticles=$p.ajax.getVal(response,'nbarticles','int',false,0);
		l_s+='<a href="#" onclick="$p.article.load();return false;">'+l_nbarticles+lg('nbArticlesToRead')+'</a>';

		$p.print("articlestoread",l_s);
	},
	//ancre3
	/*
		Function: $p.article.getRated
			get if there are rated articles
	*/
	getRated:function()
	{
	if ($p.app.user.id==0 && __useRating)	
			$('library').set('html',lg('msgNeedToBeConnectedMenu'));
	if ($p.app.user.id > 0 && __useRating)
		{
			$p.ajax.call(pep["xml_display_library"],
				{	
					'type':'load',
					'callback':
					{
						'function':$p.article.displayRated
					}
				}
			);
		}
	},
	/*
		Function: $p.article.displayRated
			display the link to open the library
	
		Parameters:
		
			response - XML object
			vars (array) - variables (optionnal)
	*/
	displayRated:function(response,vars)
	{
		var l_s='';
		l_s+='<a href="#" onclick="$p.article.loadLibrary();return false;">'+lg('library')+'</a>';

		$p.print("library",l_s);
	}
}

//send articles to read to the notebook
$p.article.classify={
	redirect:true,
	/*
		$p.article.classify.newDocument : display classification box for a new article
		inputs : article javascript ID
	*/
	newDocument:function(v_id)
	{
		var l_s = "<form onsubmit='return $p.article.classify.save(this,true);'>"
            + "<input type='hidden' name='articleurl' value='"+$p.article.items[v_id].link+"' />"
            + "<br />"
            + $p.article.classify.information($p.article.items[v_id].title,
                                              $p.article.items[v_id].id,
                                              "",
                                              "",
                                              3,
                                              $p.article.items[v_id].icon,
                                              $p.article.items[v_id].source,
                                              $p.article.items[v_id].date,
                                              $p.article.items[v_id].feedArticleId)
            + "<br />"
            + "<center><input type='submit' value='"+lg("add")+"' class='submit' /> <input type='button' class='btn' value='"+lg("cancel")+"' onclick='$p.app.popup.hide()' /></center>";
            + "</form>";
		
		$p.app.popup.show(l_s,500,400,lg("archiveArticle"));
	},
	/*
		$p.article.classify.load : load article detail information
		inputs : article ID
	*/
	load:function(v_id)
	{
		$p.ajax.call(pep["xmlarticles_mydetail"]+"?id="+v_id,
			{
				'type':'load',
				'callback':
				{
					'function':$p.article.classify.show,
					'variables':
					{
						'id':v_id
					}
				}
			}
		);
	},
	/*
		$p.article.classify.show : display article detail information
		inputs : xml response
	*/
	show:function(response,vars)
	{
		var l_s = "";
		var l_title = $p.ajax.getVal(response,"title","str",false,"---");
		var l_link = $p.ajax.getVal(response,"link","str",false,"");
		var l_private = $p.ajax.getVal(response,"private","int",false,3);
		var l_desc = $p.ajax.getVal(response,"description","str",false,"");
		var l_icon = $p.ajax.getVal(response,"icon","str",false,"");
		var l_source = $p.ajax.getVal(response,"source","str",false,"---");
		var l_date = $p.ajax.getVal(response,"pubdate","str",false,"0000-00-00");
		var l_feedarticleid = $p.ajax.getVal(response,"feedarticle_id","int",false,0);
		//get user keywords
		var l_kw=[],l_result=response.getElementsByTagName("keyword");
		for (var i=0;i<l_result.length;i++)
		{
			l_kw.push(l_result[i].firstChild.nodeValue);
		}
		l_keywords=l_kw.join(",");
				
		var l_s="<form onsubmit='return $p.article.classify.save(this,false);'><input type='hidden' name='articleurl' value='"+l_link+"' /><br />";
		l_s+=$p.article.classify.information(l_title,vars['id'],l_keywords,l_desc,l_private,l_icon,l_source,l_date,l_feedarticleid);
		l_s+="<br /><input type='submit' value='"+lg("modify")+"' class='submit' /> <input type='button' class='btn' value='"+lg("cancel")+"' onclick='$p.app.popup.hide()' />";
		l_s+="</form>";
		
		$p.app.popup.show(l_s,500,400,lg("archiveArticle"));
	},
	/*
		$p.article.classify.information : generate the details informaiton about the article
		inputs :
			v_title : article title
			v_id : article ID
			v_keywords : article keywords
			v_description : article description
			v_access : 1=private / 3=public / 2=network
			v_icon : article icon
			v_source : article source
			v_date : article pubdate
			v_feedArticleId : article ID in the feed_article table
	*/
	information:function(v_title,v_id,v_keywords,v_description,v_access,v_icon,v_source,v_date,v_feedArticleId)
	{
		var l_s = "<br /><br />"
            + "<h2><img src='"+v_icon+"' class='imgmid'> "+v_title+"</h2><br /><br />"
            + '<input type="hidden" name="aid" value="'+v_id+'" />'
            + '<input type="hidden" name="title" value="'+v_title+'" />'
            + '<input type="hidden" name="icon" value="'+v_icon+'" />'
            + '<input type="hidden" name="source" value="'+v_source+'" />'
            + '<input type="hidden" name="pubdate" value="'+v_date+'" />'
            + '<input type="hidden" name="feedarticleid" value="'+v_feedArticleId+'" />'
            + "<center><input type='radio' name='priv' value='3' "+(v_access==3?"checked='checked' ":"")+"/>"+lg("public")+" <input type='radio' name='priv' value='2' "+(v_access==2?"checked='checked' ":"")+"/>"+lg("myNetwork")+" <input type='radio' name='priv' value='1' "+(v_access==1?"checked='checked' ":"")+"/>"+lg("private")+"</center><br />"
            + lg("tags")+" "+tooltip("helpTagsArticle")+"<br />"
            + "<input class='thinbox' type='text' id='articletagsinput' name='keywords' maxlength='255' value='"+v_keywords+"' onkeyup=\"$p.tags.autocompletion.get('articletagsinput')\" onblur='$p.tags.autocompletion.hide()' style='width: 460px' /><br /><br />"
            + lg("description")+"<br />"
            + "<textarea name='description' style='width: 460px' rows='5'>"+v_description+"</textarea><br /><br />";
		
		return l_s;
	},
	/*
		$p.article.classify.save : classify the article in the notebook
		inputs :
			v_form : form name where classification information is
			v_owner : owner of the article classified
	*/
	save:function(v_form,v_add,v_owner)
	{
		if (v_owner==indef) v_owner=0;
		var l_title=v_form.title.value;
		var l_desc=v_form.description.value;
		var l_link=v_form.articleurl.value;
		var l_source=v_form.source.value;
		var l_date=v_form.pubdate.value;
		var l_feedArticleId=v_form.feedarticleid.value;
		var l_access=$p.app.tools.getRadioValue(v_form.priv);
		var l_keywords=$p.tags.formatList(v_form.keywords.value);

		$p.ajax.call(pep["scr_article_classify"],
			{
				'type':'execute',
				'variables':"act="+(v_add?"add":"upd")+"&id="+v_form.aid.value+"&faid="+l_feedArticleId+"&owner="+v_owner+"&priv="+l_access+"&kw="+l_keywords+"&kwformated="+$p.string.formatForSearch(l_keywords)+"&title="+$p.string.esc(l_title)+"&icon="+v_form.icon.value+"&link="+l_link+"&desc="+l_desc+"&source="+$p.string.esc(l_source)+"&dat="+$p.date.formatDateShort($p.date.convertFromDb(l_date)),
				'alarm':false,
				'forceExecution':false,
				'callback':
				{
					'function':$p.article.classify.close
				}
			}
		);

		return false;
	},
	/*
		$p.article.classify.close : close the classification plugin window
		inputs :
	*/
	close:function(v_act)
	{
		$p.app.popup.hide();
		if (v_act=="add" && $p.article.classify.redirect) $p.article.load();
	}
};

$p.search={
	shown:false,
	searchtxt:"",
	/*
		$p.search.init : init search plugin
	*/
	init:function(searchtxt)
	{
		if (searchtxt == indef)
            searchtxt = "";

		$p.app.setState("$p.search.init()");
		$p.app.newEnv('search');
		$p.article.classify.redirect = false;

		$p.app.tabs.openTempTab(3,"$p.plugin.openInTab(%tabid%,function(){},'search/"+$p.string.formatForSearch(searchtxt)+"')",lg('Search'),'../images/ico_search.gif');

		var l_s = $p.html.buildTitle(lg('Search'))
			+ '<br /><br />'
			+ '<center>'
			+ '<form name="searchbox" id="searchbox" onsubmit="$p.search.start(this);return false;">'
			+ '<input type="text" id="globalsearchtxt" name="globalsearchtxt" size="50" maxlength="100" onkeyup=\'$p.tags.autocompletion.get("globalsearchtxt")\' onblur="$p.tags.autocompletion.hide()" value=\''+searchtxt+'\' />'
			+ '<input type="submit" class="submit" value="'+lg("Search")+'" />'
			+ '<br /><br />';
/*search in tabs removed because confusing
		l_s+='<input type="checkbox" name="inmypage" onclick="$p.search.check(\'inmypage\',this.checked)"';
		if ($p.cookie.get('inmypage')=='checked'){ l_s+=' checked="checked"';}
		l_s+=' /> '+lg('inMyOpenPages');
*/
        if (__useNotebook)
        {
    		l_s += '<input type="checkbox" name="inmyarchives" onclick="$p.search.check(\'inmyarchives\',this.checked)"';
    		if ($p.cookie.get('inmyarchives') == 'checked')
                l_s+=' checked="checked"';
    		l_s += ' /> '+lg('inMyArchives');

    		l_s += '<input type="checkbox" name="inothersarchives" onclick="$p.search.check(\'inothersarchives\',this.checked)"';
    		if ($p.cookie.get('inothersarchives') == 'checked')
                l_s += ' checked="checked"';
    		l_s += ' /> '+lg('inPeopleArchives');
        }

		l_s += '<input type="checkbox" name="inmodules" onclick="$p.search.check(\'inmodules\',this.checked)"';
		if ($p.cookie.get('inmodules') == 'checked')
            l_s += ' checked="checked"';
		l_s += ' /> '+lg('inModules');

        if (__useNetwork)
        {
    		l_s += '<input type="checkbox" name="inpeople" onclick="$p.search.check(\'inpeople\',this.checked)"';
    		if ($p.cookie.get('inpeople') == 'checked')
                l_s += ' checked="checked"';
    		l_s += ' /> '+lg('people');
        }

		l_s += '</form>'
			+ '</center>'
			+ '<br /><br />'
			+ '<div id="searchresults"></div>';
	
		$p.print('modules'+tab[$p.app.tabs.sel].id,l_s);
		
		//Set the first default filter as checked if none is cheched
		//if( (!($p.cookie.get('inmypage'))) && (!($p.cookie.get('inmyarchives'))) && (!($p.cookie.get('inothersarchives'))) && (!($p.cookie.get('inmodules'))) && (!($p.cookie.get('inpeople'))))
		if( (!($p.cookie.get('inmyarchives'))) && (!($p.cookie.get('inothersarchives'))) && (!($p.cookie.get('inmodules'))) && (!($p.cookie.get('inpeople'))) && __useNotebook)
		{
			document.searchbox.inmyarchives.checked = true;
			$p.search.check('inmyarchives',true);
		}
		
		$p.search.shown=true;
	},
	/*
		$p.search.check : write the  checkbox value in a cookie
	*/
	check:function(boxname,val)
	{
		if (val)
		{
			$p.cookie.write(boxname+'=checked');
		}
		else{
			$p.cookie.write(boxname+'=');
		}
	},
	/*
		$p.search.start : launch search
		inputs : form containing searched information
	*/
	start: function(v_form,v_def)
	{
		var l_s = "";

		if (!$p.search.shown) $p.search.init(v_def);

		if (v_form==indef) v_form = document.forms["searchbox"];

		var searchtxt = _lc(v_form.globalsearchtxt.value);

		if (searchtxt.length < 3)
		{
			$p.app.alert.show(lg("lblSrch3car"),2);
			return false;
		}
/*		if (v_form.inmypage.checked)
		{
			l_s+="<div class='searchtitle' style='position: relative'><a style='float: right;padding-right:4px' href='#' onclick=\"$p.search.close('inmypage','resultsinmypage')\">"+$p.img("ico_close.gif",12,11)+"</a>"+lg("Search")+" :: "+lg("inMyOpenPages")+"</div><div id='resultsinmypage'></div>";
		}
*/
		if (__useNotebook && (v_form.inmyarchives.checked || v_form.inothersarchives.checked))
		{
			l_s += $p.html.buildTitle(lg("Search")+" :: "+lg("inPeopleArchives"),"<a href='#' onclick=\"$p.search.close('inmyarchives','resultsinmyarchives');$p.search.close('inothersarchives','resultsinothersarchives');\">"+$p.img("ico_close.gif",12,11)+" "+"</a>")
                + "<div id='resultsinarchives'></div>"
                + "<br /><br /><a href='#' onclick=\"$p.app.widgets.rss.checkFeed('"+__LOCALFOLDER+"portal/"+pep["rss_notebooksearch"]+"?search="+searchtxt+"',lg('searchResultsFor'),'x')\">"+$p.img('mymodules_add.gif',16,16,lg('addThisModuleInMyPage'),'imgmid')+" "+lg('addThisModuleInMyPage')+"</a><br /><br />";                
		}
		if (v_form.inmodules.checked)
		{
			l_s += $p.html.buildTitle(lg("Search")+" :: "+lg("inModules"),"<a href='#' onclick=\"$p.search.close('inmodules','resultsinmymodules')\">"+$p.img("ico_close.gif",12,11)+"</a>")
                + "<div id='resultsinmymodules'></div>";
		}
		if (__useNetwork && v_form.inpeople.checked)
		{
			l_s += $p.html.buildTitle(lg("Search")+" :: "+lg("people"),"<a href='#' onclick=\"$p.search.close('inpeople','resultsinpeople')\">"+$p.img("ico_close.gif",12,11)+"</a>")
                + "<div id='resultsinpeople'></div>";
		}
		$p.print("searchresults",l_s);

/*		if (v_form.inmypage.checked)
		{
			navWait("resultsinmypage");
			$p.search.page.init(searchtxt);
		}
*/
		if (__useNotebook && (v_form.inmyarchives.checked || v_form.inothersarchives.checked))
		{
			navWait("resultsinarchives");

            var l_searchType = 0;
            if (v_form.inmyarchives.checked) l_searchType += 1;
            if (v_form.inothersarchives.checked) l_searchType += 2;
			$p.search.notebook.load($p.string.formatForSearch(searchtxt),0,l_searchType);
		}
		if (v_form.inmodules.checked)
		{
			navWait("resultsinmymodules");
			$p.search.module.load($p.string.formatForSearch(searchtxt),0);
		}
		if (__useNetwork && v_form.inpeople.checked)
		{
			navWait("resultsinpeople");
			$p.search.network.load($p.string.formatForSearch(searchtxt),0);
		}
		return false;								
	},
	/*
		$p.search.close : hide a search window
		inputs :
			v_type : search type to hide
			v_div : div object containing search results
	*/
	close: function(v_type,v_div)
	{
		var l_form = document.forms["search"];
		l_form.elements[v_type].checked = false;
		$p.print(v_div,"");
	}
}
$p.search.page={
	nbPerPage:5,
	results:[],
	resultObj:function(tabId,artId,step)
	{
		this.tabId=tabId;
		this.artId=artId;
		this.step=step;
	},
	/*
		$p.search.page.init : init search in active pages
		inputs : searched string
	*/
	init:function(v_searchtxt)
	{
		$p.search.searchtxt=v_searchtxt;
		$p.search.page.results.length=0;
		$p.search.page.load(1,0);
	},
	/*
		$p.search.page.load : search string in active pages
		inputs :
			v_step : search step
			v_start : start at result nb v_start
	*/
	load:function(v_step,v_start)
	{
		l_searchtxt=$p.search.searchtxt;
		if (v_step==indef) v_step=1;
		if (v_start==indef) v_start=0;
		
		if (    $p.search.page.results.length < (v_start+$p.search.page.nbPerPage) 
                && v_step!=7
            )
		{
			//search exact sentence in title
			if (v_step==1)
			{
				for (var i=0;i<tab.length;i++)
				{
					if (tab[i].isLoaded)
					{
                        for (var l_modId in tab[v_tab].feeds) {
                            if ( typeof(tab[v_tab].feeds[l_modId]) == "object") {
                                for ( var RssArticleId in tab[v_tab].feeds[l_modId] ) {
                                    if (  (_lc(tab[i].RssArticles[j].RssArticleId.title)).indexOf(l_searchtxt)!=-1 ) {
                                        $p.search.page.results.push(new $p.search.page.resultObj(l_modId,RssArticleId,v_step));
                                    }
                                }
                            }
                        }                        
					}
				}
				v_step=2;
			}
		
			//search exact sentence in description
			if (v_step==2 && $p.search.page.results.length<=v_start+$p.search.page.nbPerPage+1)
			{
				for (var i=0;i<tab.length;i++)
				{
					if (tab[i].isLoaded)
					{
						for (var j=0;j<tab[i].feeds.length;j++)
						{
							if ((_lc(tab[i].feeds[j].desc)).indexOf(l_searchtxt)!=-1)
							{
								if ($p.search.page.noDouble(i,j)) $p.search.page.results.push(new $p.search.page.resultObj(i,j,v_step));
							}
						}
					}
				}
				v_step=3;
			}
			
			if (v_step>2) var keyword=l_searchtxt.split(" ");
			
			//search all the words in title
			if (v_step==3 && $p.search.page.results.length<=v_start+$p.search.page.nbPerPage+1)
			{
				var l_found;
				for (var i=0;i<tab.length;i++)
				{
					if (tab[i].isLoaded)
					{
						for (var j=0;j<tab[i].feeds.length;j++)
						{
							l_found=true;
							for (var k=0;k<keyword.length;k++)
							{
								if (keyword[k].length>2 && l_found)
								{
									if ((_lc(tab[i].feeds[j].title)).indexOf(keyword[k])==-1) l_found=false;
								}
							}
							if (l_found && $p.search.page.noDouble(i,j)) $p.search.page.results.push(new $p.search.page.resultObj(i,j,v_step));
						}
					}
				}
				v_step=4;
			}
		
			//search all the words in description
			if (v_step==4 && $p.search.page.results.length<=v_start+$p.search.page.nbPerPage+1)
			{
				var l_found;
				for (var i=0;i<tab.length;i++)
				{
					if (tab[i].isLoaded)
					{
						for (var j=0;j<tab[i].feeds.length;j++)
						{
							l_found=true;
							for (var k=0;k<keyword.length;k++)
							{
								if (keyword[k].length>2 && l_found)
								{
									if ((_lc(tab[i].feeds[j].desc)).indexOf(keyword[k])==-1) l_found=false;
								}
							}
							if (l_found && $p.search.page.noDouble(i,j)) $p.search.page.results.push(new $p.search.page.resultObj(i,j,v_step));
						}
					}
				}
				v_step=5;
			}
		
			//search at least 1 word in title
			if (v_step==5 && $p.search.page.results.length<=v_start+$p.search.page.nbPerPage+1)
			{
				var l_found;
				for (var i=0;i<tab.length;i++)
				{
					if (tab[i].isLoaded)
					{
						for (var j=0;j<tab[i].feeds.length;j++)
						{
							l_found=false;
							for (var k=0;k<keyword.length;k++)
							{
								if (keyword[k].length>2 && !l_found)
								{
									if ((_lc(tab[i].feeds[j].title)).indexOf(keyword[k])!=-1) l_found=true;
								}
							}
							if (l_found && $p.search.page.noDouble(i,j)) $p.search.page.results.push(new $p.search.page.resultObj(i,j,v_step));
						}
					}
				}
				v_step=6;
			}
		
			//search at least 1 word in description
			if (v_step==6 && $p.search.page.results.length<=v_start+$p.search.page.nbPerPage+1)
			{
				var l_found;
				for (var i=0;i<tab.length;i++)
				{
					if (tab[i].isLoaded)
					{
						for (var j=0;j<tab[i].feeds.length;j++)
						{
							l_found=false;
							for (var k=0;k<keyword.length;k++)
							{
								if (keyword[k].length>2 && !l_found)
								{
									if ((_lc(tab[i].feeds[j].desc)).indexOf(keyword[k])!=-1) l_found=true;
								}
							}
							if (l_found && $p.search.page.noDouble(i,j)) $p.search.page.results.push(new $p.search.page.resultObj(i,j,v_step));
						}
					}
				}
				v_step=7;
			}
		}
		
		//treat results
		var l_s="<table>",inc=v_start;
		while (inc<$p.search.page.results.length && inc<v_start+$p.search.page.nbPerPage)
		{
			l_s+="<tr>";
			if (tab[$p.search.page.results[inc].tabId].feeds[$p.search.page.results[inc].artId].image!="x") 
            {
                l_s+="<td><img"+tab[$p.search.page.results[inc].tabId].feeds[$p.search.page.results[inc].artId].image+" align='left' style='margin-right:6px;width:100px;' /></td>";
            }    
			else
            {    
                l_s+="<td valign='top' style='padding-top: 7px;'>"+$p.img("puce.gif",3,5)+"</td>";
                l_s+="<td valign='top'><a href='"+tab[$p.search.page.results[inc].tabId].RssArticles[$p.search.page.results[inc].artId].link+"' target='_blank'>"
                    +"<b>"+tab[$p.search.page.results[inc].tabId].RssArticles[$p.search.page.results[inc].artId].title+"</b></a>"
                    +"<br />"+$p.date.formatDelai($p.date.delayFromNow(tab[$p.search.page.results[inc].tabId].RssArticles[$p.search.page.results[inc].artId].date))+"</td>";
                l_s+="</tr>";
            }
			inc++;
		}
		l_s+="<tr><td></td><td align='right'>";
		if (v_start>0) l_s+="<a href='#' onclick='return $p.search.page.load("+v_step+","+(v_start-$p.search.page.nbPerPage)+")'>"+$p.img("ico_previous2.gif",12,11,"","imgmid")+" "+lg("previousResults")+"</a>";
		if ($p.search.page.results.length>v_start+$p.search.page.nbPerPage) l_s+=" &nbsp; <a href='#' onclick='return $p.search.page.load("+v_step+","+(v_start+$p.search.page.nbPerPage)+")'>"+lg("nextResults")+" "+$p.img("ico_next2.gif",12,11,"","imgmid")+"</a>";
		l_s+="</td></tr>";
		l_s+="</table>";
		if ($p.search.page.results.length==v_start) l_s+=lg("noResultForThisSearch");
		$p.print("resultsinmypage",l_s);
		
		return false;
	},
	/*
		$p.search.page.noDouble : remove doubloons from the results
		inputs :
			v_tabId : tab ID where the result was found
			v_articleID : article ID
	*/
	noDouble:function(v_tabId,v_artId)
	{
		for (var inc=0;inc<$p.search.page.results.length;inc++)
		{
			if ($p.search.page.results[inc].tabId==v_tabId && $p.search.page.results[inc].artId==v_artId) return false;
		}
		return true;
	}
}

//search in notebooks
$p.search.notebook={
	/*
		$p.search.notebook.load : search in the notebooks
		inputs :
			v_search : searched string
			v_page : results page number
			v_type : search type 1=my notebooks, 2=other notebooks 3=all
	*/
	load:function(v_search,v_page,v_type)
	{
		$p.ajax.call(pep["xmlnotebook_search"]+"?searchtxt="+v_search+"&p="+v_page+"&type="+v_type,
			{
				'type':'load',
				'callback':
				{
					'function':$p.search.notebook.display,
					'variables':
					{
						'search':v_search,
						'page':v_page,
						'type':v_type
					}
				}
			}
		);
		return false;
	},
	/*
		$p.search.notebook.display : display search results
		inputs : xml response
	*/
	display: function(response,vars)
	{
		var l_s = '<table>';
		var l_result = response.getElementsByTagName("item");

		for (var i = 0;i < l_result.length;i ++)
		{
			if (i < 10)
			{
				var l_userid = $p.ajax.getVal(l_result[i],"ownerid","int",false,0),
                    l_id = $p.ajax.getVal(l_result[i],"id","int",false,0),
                    l_owner = $p.ajax.getVal(l_result[i],"owner","str",false,""),
                    l_picture = $p.ajax.getVal(l_result[i],"picture","str",false,"");

				l_s += '<tr>'
					+ '<td valign="top" style="padding-top: 3px;">'
					+ $p.img("puce.gif",3,5,"","imgmid")
					+ '</td>'
					+ '<td valign="top">'
					+ '<a href="#" onclick=\'$p.notebook.open('+l_userid+',"note","'+l_owner+'",indef,'+l_id+',"'+l_picture+'");return false;\'>'
					+ '<b>'+$p.ajax.getVal(l_result[i],"title","str",false,"")+'</b>'
					+ '</a>'
					+ $p.date.formatDelai($p.ajax.getVal(l_result[i],"date","str",false,""));

				//options not displayed if looking in my notebook
				if (l_userid != $p.app.user.id)
				{
					l_s += '<br />'
						+ lg("sharedBy")
						+ ' <a href="#" onclick=\'$p.notebook.open('+l_userid+',"note","'+l_owner+'",indef,indef,"'+l_picture+'");return false;\'>'
						+ l_owner
						+ '</a>';
				}
				l_s += '</td>'
					+ '</tr>';
			}
		}
		if (l_result.length == 11 || vars['page'] != 0)
		{
    		l_s += '<tr>'
    			+ '<td>'
    			+ '</td>'
    			+ '<td align="right">'
    			+ $p.html.buildPageNavigator('previousResults',(vars['page']==0 ? '' : "return $p.search.notebook.load(\""+vars['search']+"\","+(vars['page']-1)+")"),'','nextResults',(l_result.length<11 ? '' : "return $p.search.notebook.load(\""+vars['search']+"\","+(vars['page']+1)+")"))
    			+ '</td>'
    			+ '</tr>';
		}
		l_s += '</table>';

        $p.print('resultsinarchives',(l_result.length == 0 ? lg("noResultForThisSearch") : l_s));
	}
}
$p.search.network={
	/*
		$p.search.network : search in users list
		inputs :
			v_search : search string
			v_page : results page number
	*/
	load:function(v_search,v_page)
	{
		$p.ajax.call(pep["xmlnetwork_search"]+"?p="+v_page+"&search="+v_search+"&type=t",
			{
				'type':'load',
				'callback':
				{
					'function':$p.search.network.display,
					'variables':
					{
						'search':v_search,
						'page':v_page
					}
				}
			}
		);
	},
	/*
		$p.search.network.display : display search results
		inputs : xml response
	*/
	display:function(response,vars)
	{
		var l_s='<table>';
		var l_result=response.getElementsByTagName("user");

		for (var i=0;i<l_result.length;i++)
		{
			if (i<10)
			{
				var l_picture=$p.ajax.getVal(l_result[i],"picture","str",false,"");
				var l_username=$p.ajax.getVal(l_result[i],"name","str",false,"");

				l_s+='<tr>'
					+'<td valign="top" width="40" height="40">'
                    +'<div class="picture_image_medium">'
                    + '<img src="'+(l_picture=="" ? "../images/nopicture.gif":l_picture)+'" />'
                    + '</div>'
                    + '<div class="picture_frame_white_medium"> </div>'
					+'</td>'
					+'<td valign="top">'
					+'<a href="#" onclick="$p.network.card.load('+$p.ajax.getVal(l_result[i],"id","int",false,0)+')">'
					+'<b>'+l_username+'</b>'
					+'</a>'
					+'<br />'
					+'<a href="#" onclick=\'$p.notebook.open('+$p.ajax.getVal(l_result[i],"id","int",false,0)+',"note","'+l_username+'",indef,indef,"'+l_picture+'")\'>'
					+lg("seeHisNotebook")
					+'</a>'
					+'</td>'
					+'</tr>';
			}
		}
		if (l_result.length==11 || vars['page']!=0)
		{
			l_s+='<tr>'
				+'<td>'
				+'</td>'
				+'<td align="right">'
				+$p.html.buildPageNavigator('previousResults',(vars['page']==0 ? '' : "return $p.search.network.load("+vars['search']+","+(vars['page']-1)+")"),'','nextResults',(l_result.length<11 ? '' : "return $p.search.network.load("+vars['search']+","+(vars['page']+1)+")"))
				+'</td>'
				+'</tr>'
		}
			l_s+='</table>';
		if (l_result.length==0) l_s+=lg("noResultForThisSearch");
		
		$p.print("resultsinpeople",l_s);
	}
}
$p.search.module={
	/*
		$p.search.module.load : search in widgets list
		inputs :
			v_search : searched string
			v_page : results page number
	*/
	load:function(v_search,v_page)
	{
		$p.ajax.call(pep["xmlsearch"]+"?p="+v_page+"&searchtxt="+v_search,
			{
				'type':'load',
				'callback':
				{
					'function':$p.search.module.display,
					'variables':
					{
						'search':v_search,
						'page':v_page
					}
				}
			}
		);
	},
	/*
		$p.search.module.display : display search results
		inputs : xml response
	*/
	display:function(response,vars)
	{
		var l_s="<table>", l_result=response.getElementsByTagName("item");
		for (var i=0;i<l_result.length;i++)
		{
			if (i<10)
			{
				l_s+="<tr>";
				l_s+="<td valign='top'>"+$p.img("puce.gif",3,5,"","imgmid")+" <a href='#' onclick=\"$p.app.widgets.open("+$p.ajax.getVal(l_result[i],"id","int",false,0)+",'','uniq',"+($p.ajax.getVal(l_result[i],"secured","int",false,0)==0?false:true)+")\"><b>"+$p.ajax.getVal(l_result[i],"name","str",false,"")+"</b></a></td>";
				l_s+="</tr>";
			}
		}
		if (l_result.length==11 || vars['page']!=0)
		{
			l_s+='<tr>'
				+'<td>'
				+'</td>'
				+'<td align="right">'
				+$p.html.buildPageNavigator('previousResults',(vars['page']==0 ? '' : "return $p.search.module.load("+vars['search']+","+(vars['page']-1)+")"),'','nextResults',(l_result.length<11 ? '' : "return $p.search.module.load("+vars['search']+","+(vars['page']+1)+")"))
				+'</td>'
				+'</tr>'
		}
		l_s+='</table>';

		if (l_result.length==0) l_s+=lg("noResultForThisSearch");

		$p.print("resultsinmymodules",l_s);
	}
}
$p.network.initMenu=function()
{
	if ($p.app.user.id>0)
    {
        $p.app.menu.options.push(
            {
                "id":"network",
                "label":lg("myNetwork"),
                "desc":lg("networkIconDesc"),
                "icon":"ico_menu_share.gif",
                "seq":40,
                "action":"",
                "type":"",
                "pages":[]
            }
        );
    }
}
/*
	$p.network.addNews : feed users NEWS FEED
	inputs : xml return from scr_notebook_articleadd.php
*/
$p.network.addNews=function(v_vars,v_fctvars)
{
	var l_vars=v_vars.split(/_/);
	var l_s="type="+l_vars[0];
	//l_s+="&access="+$p.app.tools.getRadioValue(document.forms["friend"]);
	l_s+="&access="+v_fctvars['access'];
	if (l_vars[0]==2)
	{
		l_s+="&title="+$p.string.esc($p.string.removeTags($p.article.format(tab[$p.app.tabs.sel].RssArticles[l_vars[1]].title)));
		l_s+="&link="+$p.string.esc("id="+$p.app.user.id+"&artid="+l_vars[2]);
	}
	if (l_vars[0]==3)
	{
		l_s+="&title="+$p.string.esc(tab[$p.app.tabs.sel].module[l_vars[1]].name);
		l_s+="&link="+$p.string.esc("pid="+tab[$p.app.tabs.sel].module[l_vars[1]].id+"&"+tab[$p.app.tabs.sel].module[l_vars[1]].vars);
	}
	if (l_vars[0]==4)
	{
		l_s+="&title="+$p.string.esc(tab[$p.app.tabs.sel].label);
		l_s+="&link="+$p.string.esc("id="+l_vars[2]+"&chk=");
	}
	$p.ajax.call(pep["scr_network_news"],
		{
			'type':'execute',
			'variables':l_s,
			'alarm':true
		}
	);
	//$p.app.menu.hide();
}
$p.network.dashboard={
	filterKw:0,
	labelKwList:[],
	filterUsersCorporateGroup:0,
	labelUsersCorporateGroupList:[],
	filterUsersWorkingGroup:0,
	labelUsersWorkingGroupList:[],
	centralAreaShown:"USERS",
	nbUsersInMyNetwork:0,
	nbUsersGroups:0,
	/*
		$p.network.dashboard.buildElement : build user card
		inputs :
			v_id : user ID
			v_img : user picture
			v_name : user name
			v_email : user email
			v_status : user status
			v_keywords : user keywords
			v_options : actions available for this user
			v_isCreator : used by displayUsersWorkingGroup, true if the l_id is the notebook_group's creator  
			v_activity : user activity (chat feature)
	*/
	buildElement: function(v_id,v_img,v_name,v_email,v_status,v_description,v_keywords,v_options,v_isCreator,v_activity)
	{
		var l_s = '<li>'
			+ '<div class="card-inner">'
			+ '<div class="card-content">'
			+ '<table cellpadding="8">'
			+ '<tr>'
			+ '<td valign="top" width="80">'
			+ '<a href="#" onclick="$p.network.card.load('+v_id+')">'
            + '<div class="picture_image_big">'
            + '<img src="'+v_img+'" />'
            + '</div>'
            + '<div class="picture_frame_white_big"> </div>'
			+ '</td>'
			+ '<td valign="top">';

		if (v_isCreator != indef && v_isCreator == true) 
		{
			l_s += '<div class="card-bar-creator">'
				+ '<b>'+lg("manager")+'</b>'
				+ '&nbsp;-&nbsp;';
		}
		else
		{
			l_s += '<div class="card-bar">';
		}
		
		if (__useChat && v_activity != indef)
		{
			l_s += $p.img('ico_activity'+v_activity+'.gif',16,16,lg('activity'+v_activity),'imgmid')+' ';
		}

		l_s += '<b>'
            + '<a href="#" onclick="$p.network.card.load('+v_id+')">'
            + v_name
            + '</a>'
            + '</b>';

		if (v_status != '' && v_status != ' -')
		{
			l_s += ' : '+v_status; //$p.string.trunk(v_status,30)
		}
		l_s += '</div>'
			+ '<br />'
			+ (checkEmail(v_email) ? $p.img('ico_mail_unread.gif',16,16,'','imgmid')+' <a href="mailto:'+v_email+'" title="'+v_email+'" >'+lg('contactByEmail')+'</a>' : '');

		if (__useNotebook)
		{
			l_s += ' | '
				+ $p.img("ico_notebook.gif",16,16,lg("seeHisNotebook"),"imgmid")
				+ ' <a href="#" onclick=\'$p.notebook.open('+v_id+',"note","'+v_name+'",indef,indef,"'+v_img+'")\'>'
				+ lg("seeHisNotebook")
				+ '</a>';
		}
		if (__useChat && v_activity == 'o' && v_id != $p.app.user.id)
		{
			l_s += ' | '
				+ $p.img("ico_chat.gif",16,16,'',"imgmid")
				+ '<a href="#" onclick=\'$p.chat.discussion.open(0,'+v_id+',"'+v_name+'","'+v_img+'");\'>'
				+ lg("chat")+'</a>';
		}
		for (var i = 0;i < v_options.length;i ++)
		{
			l_s += ' | '
                + v_options[i];
		}
		l_s += '<br /><br /'
            + '</td>'
			+ '</tr>'
			+ '</table>'
			+ '</div>'
			+ '</div>'
			+ '</li>';
	
		return l_s;
	},
	/*
		$p.network.dashboard.detailInfos : Load an iframe which displays user's detailed infos
	*/
	detailInfos:function(v_id)
	{
		$p.ajax.call(pep["xmlnetwork_completeInfos"]+'?id='+v_id,
			{
				'type':'load',
				'callback':
				{
					'function':$p.network.dashboard.displayDetailInfos
				}
			}
		);
	},
	displayDetailInfos: function(response,vars)
	{
		//general user info
		var v_id = $p.ajax.getVal(response,'id','int',false,0),
            picture = $p.ajax.getVal(response,'picture','str',false,''),
            description = $p.ajax.getVal(response,'description','str',false,''),
            username = $p.ajax.getVal(response,'username','str',false,''),
            longname = $p.ajax.getVal(response,'longname','str',false,''),
            keywords = $p.ajax.getVal(response,'keywords','str',false,''),
            networknb = $p.ajax.getVal(response,'innetwork','int',false,0),
            mydescription = $p.ajax.getVal(response,'mydescription','str',false,'');
        
		//get keywords i set for this user
		var l_kw = [],
            l_result2 = response.getElementsByTagName("mykeywords");

		for (var i = 0;i < l_result2.length;i ++)
		{
			l_kw.push(l_result2[i].firstChild.nodeValue);
		}
		if (l_kw.length == 0)	l_kw.push('');			
		
		//popup content
		var l_s = ''
            + '<table border="0" cellspacing="3" cellpadding="3" width="95%">'
            + '<tr>'
			+ '<td align="center" valign="top" width="100">'
			+ '<img src='+picture+'?nocache='+rand+' width="64" height="64" class="picture" />'
			+ '</td>'
			+ '<td valign="top">'
			+ '<div class="title">'+lg('publicInfo')+'</div>'
			+ (checkEmail(username) ? '<p><b>'+lg('lblEmail')+': </b> '+username+'</p>' : '')
			+ '<p><b>'+lg('Name')+': </b> '+longname+'</p>'
			+ '<p><b>'+lg('tags')+': </b>'+keywords+'</p>'
			+ '<p><b>'+lg('desc')+': </b>'+description+'</p>';
          
        
		l_s += $p.network.dashboard.buildCriteria(response);

		if (networknb == 1)   {
			l_s += '<div class="title">'+lg('privateInfo')+'</div>'
				+ '<p><b>'+lg('myTags')+' :</b>'+l_kw.join(",")+'</p>'
				+ '<p><b>'+lg('description')+' :</b>'+mydescription+'</p>'
                + '<a href="#" onclick="$p.network.card.load('+v_id+')">'+lg('modify')+'</a>';
		}

		l_s += '</td>'
			+ '</tr>'
			+ '</table>';

		if (networknb == 0)   {
			l_s += '<center><input type="button" class="btn" onclick="return $p.network.card.load('+v_id+',true)" value="'+lg('addToMyNetwork')+'" /></center>';
		}

		$p.app.popup.show(l_s,600,indef,lg("completeDetails"),true);
	},
	buildCriteria:function(response)
	{
		var l_s='';
		//specific user caracteristics (criterias)
		var result=response.getElementsByTagName("criteria");
		for (var i=0;i<result.length;i++)
		{
			var type=$p.ajax.getVal(result[i],'type','int',false,0);
			var label=$p.ajax.getVal(result[i],'label','str',false,'');
			var parameters=$p.ajax.getVal(result[i],'parameters','str',false,'undefined');
			var options=$p.ajax.getVal(result[i],'options','str',false,'');

			switch (type)
			{
				//if the criteria is an input type TEXT
				case 1 :
				{	
					l_s+="<p><b>"+label+": </b>"+parameters+"</p>";
					break;
				}			
				//if the criteria is a TEXTAREA
				case 5 :
				{
					l_s+="<p><b>"+label+": </b>"+parameters+"</p>";
					break;
				}	
				//if the criteria is a SELECT
				case 2 :
				{
					var tableau = options.split(";");
					l_s+="<p><b>"+label+": </b>"+tableau[parameters-1]+"</p>";
					break;
				}	
				//if the criteria is a RADIO
				case 4 :
				{
					var tabRadio = options.split(";")
					l_s+="<p><b>"+label+": </b>"+tabRadio[parameters-1]+"</p>";
					break;
				}		
					
				//if the criteria is a CHECKBOX
				case 3 :
				{
					var tabOptions = options.split(";");
					var tabParameters = parameters.split(";");
					
					l_s+="<p><b>"+label+": </b>";
					for (var a=0;a<tabParameters.length;a++)
					{
							var index = tabParameters[a];
							l_s+=tabOptions[index-1]+" - ";
					}
					l_s+="</p>";
					break;
				}			
			}
		}
		return l_s;
	},
	open:function()
	{
		$p.network.init(lg('network'),'network');
	},
	/*
		$p.network.dashboard.myNetwork : get my network
	*/
	myNetwork:function(v_id)
	{
		if (v_id==indef) v_id=0;

		$p.network.dashboard.open();
		$p.app.setState("$p.network.dashboard.myNetwork()");
		$p.network.buildPageMenu(2);

		$p.network.dashboard.getUsers(v_id);
	},
/*
		$p.network.dashboard.getUsers : load users of my network 
		inputs : keyword id (if filtered by keyword)
	*/
	getUsers:function(v_keywordId,v_page)
	{
		if (v_page==indef) v_page=0;

		$p.network.dashboard.filterKw=v_keywordId;
		navWait('network_content');
		$p.plugin.page='mynetwork';

		var v_title = (0==v_keywordId ? lg("peopleToMyNetwork") : lg("peopleToMyNetworkWithTag"));

		$p.ajax.call(pep["xmlnetwork_users"]+'?kwid='+v_keywordId+'&s='+(v_page*20),
			{
				'type':'load',
				'callback':
				{
					'function':$p.network.dashboard.displayUsers,
					'variables':
					{
						'v0':'',
						'v1':true,
						'v2':true,
						'showRemoveButton':true,
						'showAddBtn':false,
						'title':v_title,
						'page':v_page,
						'keyword':v_keywordId,
						'feature':'mynetwork'
					}
				}
			}
		);
	},
	/*
	* $p.network.dashboard.menu : load user's group
	
	menu:function()
	{
		$p.app.menu.addTitle('networkmenu_1','mynetwork.gif',lg("myNetwork"),$p.network.dashboard.getInfo);
		var l_s=''
			+'<div class="part">'
			+'<div class="title">'+lg("addFriend")+' :</div>'
			+'<form onsubmit="return $p.network.add.search(this)">'
			+$p.img("ico_friend_add.gif",16,16,"","imgmid")+'&nbsp;<input type="text" name="searchtxt" class="thinbox" style="width: 176px;color: #aaaaaa;" onFocus=\'$p.app.tools.inputFocus(this,"'+lg("inputEmailOrNameOrTag")+'")\' onBlur=\'$p.app.tools.inputLostFocus(this,"'+lg("inputEmailOrNameOrTag")+'");\' value="'+lg("inputEmailOrNameOrTag")+'" />'
			+'&nbsp;<input type="submit" class="submit" value="'+lg("ok")+'" style="width:22px" />'
			+'</form>'
			+'<br />'
			+$p.img('puce.gif')+" <a href='#' onclick='$p.network.dashboard.loadRecommended()'>"+lg("recommendedUsers")+"</a><br />"
			+$p.img('puce.gif')+" <a href='#' onclick='$p.network.dashboard.loadDirectory()'>"+lg("usersDirectory")+"</a><br /><br />"
			+'</div>'
		
			+'<div id="networkinfodiv"></div>'
			+'<br />';

		$p.app.menu.addArea('networkmenu_1',l_s);

		$p.app.menu.addTitle('networkmenu_2','ico_work.gif',lg("myWorkingGroups"),$p.network.dashboard.getUserWorkingGroups);
		$p.app.menu.addArea('networkmenu_2','<div id="workinggroupsdiv"></div>');

		$p.app.menu.addTitle('networkmenu_3','ico_corporate.gif',lg("myCorporateGroups"),$p.network.dashboard.getUserCorporateGroups);
		$p.app.menu.addArea('networkmenu_3','<div id="usergroupsdiv"></div>');

		$p.app.menu.addTitle('networkmenu_4','ico_tag.gif',lg('tagsFilter'),$p.network.dashboard.getkeywords);
		$p.app.menu.addArea('networkmenu_4','<div id="keywordsdiv"></div>');

		$p.app.menu.openSubMenu('networkmenu_1',true);

	},
	*/
	/*
		$p.network.dashboard.getInfo: Get information about your network
	*/
	getInfo:function()
	{
		if ($p.app.user.id==0)	$('networkinfodiv').set('html',lg('msgNeedToBeConnectedMenu'));
		else
		{
			navWait('networkinfodiv');
			$p.ajax.call(pep["xmlnetwork_info"],
				{
					'type':'load',
					'callback':
					{
						'function':$p.network.dashboard.displayInfo
					}
				}
			);
		}
	},
	displayInfo:function(response,vars)
	{
		var l_s='<br />'
			+'<div class="title">'+lg('myNetwork')+' :</div>'
			+$p.img('puce.gif')+" <a href='#' onclick='$p.network.dashboard.myNetwork()'>"+$p.ajax.getVal(response,'networknb','int',false,0)+"&nbsp;"+lg("peopleOnYourNetwork")+"</a>"
			+"<br/>"
		//l_s+="<a href='#' onclick='$p.network.chat.menu()'>"+$p.ajax.getVal(response,'onlinenb','int',false,0)+"&nbsp;personne(s) de votre réseau en ligne</a><br/>";
			+$p.img('puce.gif')+" <a href='#' onclick='$p.network.dashboard.loadFollowers()'>"+$p.ajax.getVal(response,'referernb','int',false,0)+"&nbsp;"+lg('addedinNetwork')+"</a>"
			+"<br/>"
		if (__useNetwork) l_s+=$p.img('puce.gif')+" <a href='#' onclick='$p.network.dashboard.initNetworkNews()'>"+lg("newOfYourNetwork")+"</a><br />";
		
		$p.print('networkinfodiv',l_s);
	},
	/*
		$p.network.dashboard.getkeywords : load user keywords
	*/
	getkeywords:function()
	{
		if ($p.app.user.id==0)	$('keywordsdiv').set('html',lg('msgNeedToBeConnectedMenu'));
		else
		{
			navWait("keywordsdiv");
			$p.ajax.call(pep["xmlnetwork_keywords"],
				{
					'type':'load',
					'callback':
					{
						'function':$p.network.dashboard.displayKeywords
					}
				}
			);
		}
	},
	/*
	 * $p.network.dashboard.getUsersWorkingGroup : load users of working group and display it in central area
		inputs : notebook group id
	 */
	getUsersWorkingGroup:function(v_groupId)
	{
		$p.network.dashboard.open();

		navWait('network_content');

		$p.network.dashboard.filterUsersWorkingGroup = v_groupId;
		var l_label = $p.network.dashboard.labelUsersWorkingGroupList[v_groupId];

		$p.ajax.call(pep["xmlnetwork_usersnotebookgroup"]+'?gid='+v_groupId,
			{
				'type':'load',
				'callback':
				{
					'function':$p.network.dashboard.displayUsersWorkingGroup,
					'variables':
					{
						'v0':'',
						'v1':true,
						'v2':true,
						'showRemoveButton':true,
						'showAddBtn':true,
						'title':lg("membersOfGroupname")+"&nbsp;&laquo;"+l_label+"&raquo;",
						'divid':'network_content'
					}
				}
			}
		);
	},
	/*
		$p.network.dashboard.displayUsersWorkingGroup : display users working group in the central area
		inputs : xml response
	*/
	displayUsersWorkingGroup:function(response, vars) 
	{
		var l_s = $p.html.buildTitle(vars['title'])
			+ '<ul class="card-outer">';

		var result = response.getElementsByTagName("user");		
		if (result.length == 0)
		{
			l_s += lg("noMembersOfThisGroupInYourNetwork");
		}
		else
		{
			var v_isCreator;
			for (var i = 0;i < result.length;i++)
			{
				var l_id = $p.ajax.getVal(result[i],"id","int",false,0);
				var l_picture = $p.ajax.getVal(result[i],"picture","str",false,"");
				var l_created_by = $p.ajax.getVal(result[i], "created_by", "int", false, 0);
				if (l_picture == "") l_picture = "../images/nopicture.gif";
				var l_options = [];

				if($p.app.user.id != l_id) {
					l_options.push($p.img('ico_group_add.gif',16,16,'','imgmid')+" <a onclick='$p.group.card.load("+l_id+", "+$p.app.user.id+")' href='#'>"+lg("addInMyGroups")+"</a>");
				}
				if (l_created_by == l_id) {
					v_isCreator = true;
				}
				else {
					v_isCreator = false;
				}
				if (vars['showRemoveButton']) l_options.push("<a href='#' onclick='return $p.network.suppress("+l_id+")'>"+lg("suppressFromMyNetwork")+"</a>");
				l_s += $p.network.dashboard.buildElement(l_id,l_picture,$p.ajax.getVal(result[i],"longname","str",false,"..."),$p.ajax.getVal(result[i],"username","str",false,"..."),$p.ajax.getVal(result[i],"stat","str",false,"")+' -'+$p.date.formatDelai($p.date.delayFromNow($p.date.convertFromDb($p.ajax.getVal(result[i],"statdate","str",false,"")))),$p.ajax.getVal(result[i],"description","str",false,""),$p.ajax.getVal(result[i],"keywords","str",false,""),l_options, v_isCreator,$p.chat.computeActivity($p.ajax.getVal(result[i],"activity","str",false,"x"),$p.ajax.getVal(result[i],"lastconndate","str",false,""),$p.ajax.getVal(result[i],"dbdate","str",false,"")));
			}
		}
		l_s += "</ul><div style='clear: both;float: none;'></div>";
		l_s += "<div style='text-align: center;background: #c6c3c6;height: 22px;margin-top: 15px;padding-top: 3px;'></div>";
		
		$p.print(vars['divid'], l_s);
		$p.network.dashboard.centralAreaShown = "WORK";
		
		$p.network.dashboard.refreshKeywords();
	},
	/*
	 * $p.network.dashboard.getUsersCorporateGroup : load users group and display it in central area
		inputs : group id
	 */
	getUsersCorporateGroup:function(v_groupId)
	{
		$p.network.dashboard.open();

		navWait('network_content');
		
		$p.network.dashboard.filterUsersCorporateGroup = v_groupId;
		var l_label = $p.network.dashboard.labelUsersCorporateGroupList[v_groupId];

		$p.ajax.call(pep["xmlnetwork_usersgroup"]+'?gid='+v_groupId,
			{
				'type':'load',
				'callback':
				{
					'function':$p.network.dashboard.displayUsersCorporateGroup,
					'variables':
					{
						'v0':'',
						'v1':true,
						'v2':true,
						'showRemoveButton':true,
						'showAddBtn':true,
						'label':l_label
					}
				}
			}
		);
	},
	/*
		$p.network.dashboard.displayUsersCorporateGroup : display users group in the central area
		inputs : xml response
	*/
	displayUsersCorporateGroup: function(response, vars) 
	{
		var result = response.getElementsByTagName("user"),
            l_s = $p.html.buildTitle(lg("membersOfGroupname") + "&nbsp;" + vars['label'])
                + '<ul class="card-outer">';
		
		if (result.length == 0)
		{
			l_s += lg("noMembersOfThisGroupInYourNetwork");
		}
		else
		{
			for (var i=0;i < result.length;i++)
			{
				var l_id = $p.ajax.getVal(result[i],"id","int",false,0),
                    l_picture = $p.ajax.getVal(result[i],"picture","str",false,"");
				if (l_picture=="") l_picture="../images/nopicture.gif";
				var l_options=[];
				if($p.app.user.id != l_id) {
					l_options.push($p.img('ico_group_add.gif',16,16,'','imgmid')+" <a onclick='$p.group.card.load("+l_id+", "+$p.app.user.id+")' href='#'>"+lg("addInMyGroups")+"</a>");
				}
				if (vars['showRemoveButton']) l_options.push("<a href='#' onclick='return $p.network.suppress("+l_id+")'>"+lg("suppressFromMyNetwork")+"</a>");
                var adate = $p.date.formatDelai($p.date.delayFromNow($p.date.convertFromDb($p.ajax.getVal(result[i],"statdate","str",false,""))));
				l_s+=$p.network.dashboard.buildElement(l_id,l_picture,
                                        $p.ajax.getVal(result[i],"longname","str",false,"..."),
                                        $p.ajax.getVal(result[i],"username","str",false,"..."),
                                        $p.ajax.getVal(result[i],"stat","str",false,"")+' -'+ adate,
                                        $p.ajax.getVal(result[i],"description","str",false,""),
                                        $p.ajax.getVal(result[i],"keywords","str",false,""),
                                        l_options,
                                        indef,
                                        $p.chat.computeActivity(
                                                $p.ajax.getVal(result[i],'activity','str',false,'x'),
                                                $p.ajax.getVal(result[i],'lastconndate','str',false,''),
                                                $p.ajax.getVal(result[i],'dbdate','str',false,'')
                                            ) 
                                        );
			}
		}
		l_s+="</ul><div style='clear: both;float: none;'>";
		l_s+="<div style='text-align: center;background: #c6c3c6;height: 22px;margin-top: 15px;padding-top: 3px;'>";
		
		$p.print('network_content', l_s);
		$p.network.dashboard.centralAreaShown = "CORP";
		
		$p.network.dashboard.refreshKeywords();
	},
	/*
		$p.network.dashboard.getUserCorporateGroups : load user corporate groups
	*/
	getUserCorporateGroups:function() 
	{
		$p.network.init();
		$p.network.buildPageMenu(4);

		var l_s=$p.html.buildTitle(lg('myCorporateGroups'))
			+'<div id="usergroupsdiv"></div>';
		$p.print('network_content',l_s);

		if ($p.app.user.id==0)	$('usergroupsdiv').set('html',lg('msgNeedToBeConnectedMenu'));
		else
		{
			navWait("usergroupsdiv");

			$p.ajax.call(pep["xmlnetwork_usercorporategroups"],
				{
					'type':'load',
					'callback':
					{
						'function':$p.network.dashboard.displayUserCorporateGroups
					}
				}
			);
		}
	},
	/*
		$p.network.dashboard.displayUserCorporateGroups : display user corporate groups
		inputs : xml response
	*/
	displayUserCorporateGroups:function(response,vars)
	{
		var l_s = $p.group.buildSidebar();

		var result = response.getElementsByTagName("usergroup");

		if (result.length > 0)
		{
			$p.network.dashboard.labelUsersCorporateGroupList = new Array();
			for (var i = 0;i < result.length;i++)
			{
				var l_id = $p.ajax.getVal(result[i],"id","int",false,0);
				var l_label = $p.ajax.getVal(result[i],"name","str",false,"...");
				$p.network.dashboard.labelUsersCorporateGroupList[l_id] =l_label;
				l_s += $p.group.buildElement('predefined',l_id,'',l_label,'');
			}
		}
		else
		{
			l_s += lg("noGroup");
		}

		$p.print("usergroupsdiv",l_s);
	},
	/*
		$p.network.dashboard.displayKeywords : display user keywords
		inputs : xml response
	*/
	displayKeywords:function(response,vars)
	{
		var l_s='';
		
		var result=response.getElementsByTagName("keyword");
		$p.network.dashboard.labelKwList = new Array();
		for (var i=0;i<result.length;i++)
		{
			var l_id=$p.ajax.getVal(result[i],"id","int",false,0);
			var l_label = $p.ajax.getVal(result[i],"label","str",false,"...");
			$p.network.dashboard.labelKwList[l_id]=l_label;
			if (l_id==$p.network.dashboard.filterKw) 
				l_s+="<font color='red'>"+l_label+"</font>&nbsp;";
			else 
				l_s+='<a href="#" onclick=\'$p.network.dashboard.myNetwork('+ l_id +')\'>'+l_label+'</a>&nbsp;';
		}
		if (result.length==0)
			l_s+=lg('noTag');

		if ($p.network.dashboard.filterKw!=0)
			l_s+="<a href='#' onclick='$p.network.dashboard.myNetwork();$p.network.dashboard.getkeywords();'><b>"+lg("removeTheFilter")+"</b></a><br/>";

		$p.print("keywordsdiv",l_s);
	},
	/*
		$p.network.dashboard.getUserWorkingGroups : load user working groups
	*/
	getUserWorkingGroups:function()
	{
		if ($p.app.user.id==0)	$('workinggroupsdiv').set('html',lg('msgNeedToBeConnectedMenu'));
		else
		{
			navWait("workinggroupsdiv");
			$p.ajax.call(pep["xmlnetwork_userworkinggroups"],
				{
					'type':'load',
					'callback':
					{
						'function':$p.network.dashboard.displayUserWorkingGroups
					}
				}
			);
		}
	},
	/*
		$p.network.dashboard.displayUserWorkingGroups : display user working groups
		inputs : xml response
	*/
	displayUserWorkingGroups:function(response, vars)
	{
		var l_s = $p.group.buildSidebar()
			+ '<div>'
            + '<ul class="card-outer">';

		var result=response.getElementsByTagName("workinggroup");
		$p.network.dashboard.nbUsersGroups = result.length;

		$p.network.dashboard.labelUsersWorkingGroupList = new Array();

		if (result.length==0)
		{
			l_s+=lg('noGroup');
		}
		else
		{
			for (var i=0;i<result.length;i++)
			{
				var l_id = $p.ajax.getVal(result[i], "id", "int", false, 0);
				var l_label = $p.ajax.getVal(result[i], "name", "str", false,"...");
                var l_desc = $p.ajax.getVal(result[i], "description", "str", false,"");
				var l_createdBy = $p.ajax.getVal(result[i], "created_by", "int", false, 0);
				var l_status = $p.ajax.getVal(result[i], "status", "str", false, "I");
                var l_picture = $p.ajax.getVal(result[i], "picture", "str", false, '');

				$p.network.dashboard.labelUsersWorkingGroupList[l_id] = l_label;
				
				var l_options = '';
				if ($p.app.user.id != l_createdBy)
				{
					if (l_status == 'O')
					{
						l_options+= "<a href='#' onclick=\"$p.network.dashboard.quitUserFromWorkingGroup("+ l_id +",\'"+l_label+"')\">"
							+ lg("quitGroup")
							+ '</a>'
							+ (__useNotebook ? " | <a href='#' onclick=\"$p.notebook.open("+ l_id +",'group','"+l_label+"')\">"
                                                + lg("myGroupbook")
                                                + '</a>'
                                              : '');
					}
					else
					{
						l_options += " <a href='#' onclick=\"$p.groupbook.add.join("+ l_id +",\'"+l_label+"')\">";
						if (l_status == 'I')
						{
							l_options += '<font color="red">'+lg("joinThisGroup")+'</font>';
						}
						else
						{
							l_options += lg("joinThisGroup");
						}
						l_options += "</a>"
							+ " | <a href='#' onclick='$p.network.dashboard.removeUserFromWorkingGroup("+ l_id +")'>"
							+ "<font color='red'>"
							+ lg("reject")
							+ "</font>";
					}
				}
				else if (__useNotebook)
				{
					l_options += "<a href='#' onclick=\"$p.notebook.open("+ l_id +",'group','"+l_label+"')\">"
						+ lg("myGroupbook")
                        + '</a>';
				}
				l_s += $p.group.buildElement('mygroup',l_id,(l_picture == '' ? '../images/bigicon_network.gif' : l_picture),l_label,l_desc,l_createdBy,l_status,l_options);
			}
		}
		l_s += '<ul>'
            + '<div style="clear: both;float: none;"></div>'
			+ '</div>';

		$p.print("workinggroupsdiv",l_s);
	},
	/*
		$p.network.dashboard.loadFollowers : load my followers
	*/
	loadFollowers:function(v_page)
	{
		if (v_page==indef) v_page=0;

		$p.network.dashboard.open();
		$p.app.wait('network_content');
		$p.network.buildPageMenu(2);
		
		$p.ajax.call(pep["xmlnetwork_followers"]+'?s='+(v_page*20),
			{
				'type':'load',
				'callback':
				{
					'function':$p.network.dashboard.displayUsers,
					'variables':
					{
						'v0':'',
						'v1':false,
						'v2':true,
						'showRemoveButton':false,
						'showAddBtn':true,
						'title':lg('peopleThatAddMeInTheirNetwork'),
						'feature':'followers',
						'page':v_page
					}
				}
			}
		);
	},
	/*
		$p.network.dashboard.loadDirectory : load users directory
		inputs : user first letter (if filtered by first letter)
	*/
	loadDirectory:function(v_initial,v_page)
	{
		if (v_page==indef) v_page=0;

		$p.network.dashboard.open();
		$p.network.buildPageMenu(2);

		if ($p.app.user.id==0)	$('network_content').set('html',lg('msgNeedToBeConnectedPage'));
		else
		{
			navWait('network_content');
			$p.ajax.call(pep["xmlnetwork_directory"]+'?page='+v_page+(v_initial==indef?'':'&i='+v_initial),
				{
					'type':'load',
					'callback':
					{
						'function':$p.network.dashboard.displayUsers,
						'variables':
						{
							'title':lg("usersDirectory"),
							'v1':false,
							'v2':true,
							'showRemoveButton':false,
							'showAddBtn':true,
							'initial':v_initial,
							'page':v_page,
							'feature':'directory'
						}
					}
				}
			);
		}
		return false;
	},
	/*
		$p.network.dashboard.initNetworkNews : init network NEWS FEED page
	*/
	initNetworkNews: function()
	{
        var l_s = $p.html.buildTitle(lg('newOfYourNetwork'))
            + '<table width="100%">'
            + '<tr>'
            + '<td id="newsofyournetwork" valign="top"></td>'
            + '<td valign="top" width="300">'+$p.network.dashboard.buildSidebar()+'</td>'
            + '</tr>'
            + '</table>';
    
		$p.print('network_content',l_s);
		$p.app.wait('newsofyournetwork');

		$p.network.information.summaryLoadNetwork(40,0,'newsofyournetwork');
	},
	/*
		$p.network.dashboard.displayUsers : display users list in the central area
		inputs : xml response
	*/
	displayUsers:function(response,vars)
	{
		var l_s=$p.html.buildTitle(vars['title']);

		var result=response.getElementsByTagName("user");
		if (result.length==0 && $p.network.dashboard.filterKw!=0){$p.network.dashboard.load();return;}

		if (vars['title']==lg('userDirectory'))
		{
			l_s+='<a href="#" onclick="$p.network.dashboard.loadDirectory()">.</a> ';
			for (var i=0;i<26;i++)
			{
				l_s+='<a href="#" onclick=\'$p.network.dashboard.loadDirectory("'+String.fromCharCode(97+i)+'"))\'>'+String.fromCharCode(97+i)+'</a> ';
			}
		}
		
		l_s+= $p.network.dashboard.buildSidebar()
			+ '<div>'
			+ '<ul class="card-outer">';

		if (0==$p.network.dashboard.filterKw)  {
			$p.network.dashboard.nbUsersInMyNetwork=result.length;
		}

		var l_iter=_min(result.length,18);
		if (l_iter==0)
		{
			l_s+=lg("emptyList");
		}
		else
		{
			for (var i=0;i<l_iter;i++)
			{
				var l_id=$p.ajax.getVal(result[i],"id","int",false,0);
				var l_picture=$p.ajax.getVal(result[i],"picture","str",false,"");
				if (l_picture=="") l_picture="../images/nopicture.gif";
				var l_options=[];
				//if (vars['v1']) l_options.push("<a href='#' onclick='$p.network.card.load("+l_id+",false)'>"+lg("modify")+"</a>");
				if (vars['showAddBtn']) l_options.push($p.img('ico_friend_add.gif',16,16,'','imgmid')+" <a href='#' onclick='$p.network.card.load("+l_id+")'>"+lg("addInMyNetwork")+" </a>");
				if (vars['showRemoveButton']) l_options.push("<a href='#' onclick='return $p.network.suppress("+l_id+")'>"+lg("suppressFromMyNetwork")+"</a>");
				if($p.app.user.id != l_id) {
					l_options.push($p.img('ico_group_add.gif',16,16,'','imgmid')+" <a onclick='$p.group.card.load("+l_id+", "+$p.app.user.id+")' href='#'>"+lg("addInMyGroups")+"</a>");
				}
				l_s+=$p.network.dashboard.buildElement(l_id,l_picture,$p.ajax.getVal(result[i],"longname","str",false,"..."),$p.ajax.getVal(result[i],"username","str",false,"..."),$p.ajax.getVal(result[i],"stat","str",false,"")+' -'+$p.date.formatDelai($p.date.delayFromNow($p.date.convertFromDb($p.ajax.getVal(result[i],"statdate","str",false,"")))),$p.ajax.getVal(result[i],"description","str",false,""),$p.ajax.getVal(result[i],"keywords","str",false,""),l_options,indef,$p.chat.computeActivity($p.ajax.getVal(result[i],'activity','str',false,''),$p.ajax.getVal(result[i],'lastconndate','str',false,''),$p.ajax.getVal(result[i],'dbdate','str',false,'')));
			}
		}

		l_s+= '</ul>'
			+ '</div>'
			+ '<div style="clear: both;float: none;"></div>';

		//page results management
		var l_page=vars['page']==indef?0:vars['page'].toInt();
		switch (vars['feature'])
		{
			case 'mynetwork':
				l_s+=$p.html.buildPageNavigator('previous',(l_page==0 ? '' : "$p.network.dashboard.getUsers("+vars['keyword']+","+(l_page-1)+")"),lg("page")+" "+(l_page+1),'next',(result.length>20 ? "$p.network.dashboard.loadDirectory("+vars['keyword']+","+(l_page+1)+")" : ''));
				break;
			case 'followers':
				l_s+=$p.html.buildPageNavigator('previous',(l_page==0 ? '' : "$p.network.dashboard.loadFollowers("+(l_page-1)+")"),lg("page")+" "+(l_page+1),'next',(result.length>20 ? "$p.network.dashboard.loadFollowers("+(l_page+1)+")" : ''));
				break;
			case 'directory':
				l_s+=$p.html.buildPageNavigator('previous',(l_page==0 ? '' : "$p.network.dashboard.loadDirectory("+vars['initial']+","+(l_page-1)+")"),lg("page")+" "+(l_page+1),'next',(result.length>20 ? "$p.network.dashboard.loadDirectory("+vars['initial']+","+(l_page+1)+")" : ''));
				break;
		}

		$p.print('network_content',l_s);
		$p.network.dashboard.centralAreaShown = "USERS";
		if (vars['keyword']!=indef && vars['keyword']!=0) $p.network.dashboard.getkeywords();
	},
	/*
		Function: $p.network.dashboard.buildSidebar : build network sidebar
	*/
	buildSidebar: function()
	{
		return 	'<div class="sidebar" style="height: 300px;">'
            + $p.html.roundBox('<div class="title">'+lg('myNetwork')+'</div>'
            + '<div class="content">'
            + '<a href="#" onclick="$p.network.dashboard.myNetwork();return false;">'+lg('myNetwork')+'</a><br />'
            + '<a href="#" onclick="$p.network.dashboard.initNetworkNews();return false;">'+lg('newOfYourNetwork')+'</a>'
            + '</div>'
            + '<div class="title">'+lg('addFriend')+'</div>'
            + '<div class="content">'
			+ '<a href="#" onclick="$p.network.dashboard.loadRecommended();return false;">'+lg('recommendedUsers')+'</a><br />'
			+ '<a href="#" onclick="$p.network.dashboard.loadFollowers();return false;">'+lg('addedinNetwork')+'</a><br />'
			+ '<a href="#" onclick="$p.network.dashboard.loadDirectory();return false;">'+lg('usersDirectory')+'</a><br />'
            + '</div>',
            '#0E679A',
            '260px')
            + '</div>';
	},
	/*
		Function: $p.network.dashboard.refresh : refresh users list
	*/
	refresh: function(){
		if ("USERS" == $p.network.dashboard.centralAreaShown)
		{
			$p.network.dashboard.getUsers($p.network.dashboard.filterKw);
		}
		else if ("CORP" == $p.network.dashboard.centralAreaShown) {
			$p.network.dashboard.getUsersCorporateGroup($p.network.dashboard.filterUsersCorporateGroup);
		}
		else {
			$p.network.dashboard.getUsersWorkingGroup($p.network.dashboard.filterUsersWorkingGroup);
		}
	},
	/*
		$p.network.dashboard.refreshKeywords : refresh user keywords list
	*/
	refreshKeywords:function() {
		$p.network.dashboard.filterKw = 0;
		$p.network.dashboard.getkeywords();
	},
	/*
	 *	$p.network.dashboard.removeUserFromWorkingGroup : Remove user from working group
	 */
	removeUserFromWorkingGroup:function(v_group_id)
	{
    		$p.group.reset();

    		$p.ajax.call(pep["scr_groupbook_add"],
    			{
    				'type':'execute',
    				'variables':'act=del&id='+v_group_id,
    				'alarm':false,
    				'forceExecution':false,
    				'callback':
    				{
    					'function':$p.network.dashboard.removeUserFromWorkingGroupConfirmation
    				}
    			}
    		);
	},
    removeUserFromWorkingGroupConfirmation:function()
    {
        $p.network.dashboard.getUserWorkingGroups();
    },
	/*
	 *	$p.network.dashboard.quitUserFromWorkingGroup : User unsubscribe working group
	 */
	quitUserFromWorkingGroup:function(v_group_id,v_group_name) 
	{
        var response = confirm(lg("msgQuitGroupQuestion"));

        if (response == 1)
        {
    		$p.ajax.call(pep["scr_groupbook_add"],
    			{
    				'type':'execute',
    				'variables':'act=quit&id='+v_group_id+'&name='+v_group_name,
    				'alarm':false,
    				'forceExecution':false,
    				'callback':
    				{
    					'function':$p.network.dashboard.getUserWorkingGroups
    				}
    			}
    		);
        }
	},
	/*
		$p.network.dashboard.loadRecommended : load recommended peoples
	*/
	loadRecommended:function(v_page)
	{
		if (v_page==indef) v_page=0;
		$p.network.dashboard.open();
		$p.network.buildPageMenu(2);

		if ($p.app.user.id==0)	$('network_content').set('html',lg('msgNeedToBeConnectedPage'));
		else
		{
			navWait('network_content');
			
			$p.ajax.call(pep["xmlnetwork_recommendations"]+'?p='+v_page,
				{
					'type':'load',
					'callback':
					{
						'function':$p.network.dashboard.displayRecommended,
						'variables':
						{
							'page':v_page
						}
					}
				}
			);
		}
	},
	displayRecommended:function(response,vars)
	{
		var l_s=$p.html.buildTitle(lg('recommendedUsers'));
		var l_excluded=[];

		var result1=response.getElementsByTagName("excluded");

		for (var i=0;i<result1.length;i++)
		{
			l_excluded.push($p.ajax.getVal(result1[i],"id","int",false,0));
		}

		var result=response.getElementsByTagName("user");

		var l_users=[],l_nextpage=false;
		for (var i=0;i<result.length;i++)
		{
			var l_id=$p.ajax.getVal(result[i],"id","int",false,0);
			if (l_id==$p.app.user.id) break;
			var l_picture=$p.ajax.getVal(result[i],"picture","str",false,"");
			if (l_picture=="") l_picture="../images/nopicture.gif";
			var l_name=$p.ajax.getVal(result[i],"longname","str",false,"...");
			var l_email=$p.ajax.getVal(result[i],"username","str",false,"...");
			var l_status=$p.ajax.getVal(result[i],"stat","str",false,"");
			var l_statusdate=$p.ajax.getVal(result[i],"statdate","str",false,"");
			var l_description=$p.ajax.getVal(result[i],"description","str",false,"");
			var l_kw=$p.ajax.getVal(result[i],"keywords","str",false,"");
			var l_rel=$p.ajax.getVal(result[i],"nbrel","str",false,0);
			var l_type=$p.ajax.getVal(result[i],"type","str",false,'');
			if (l_id!=$p.app.user.id && l_excluded.indexOf(l_id)==-1)
			{
				l_users.push({'id':l_id,'picture':l_picture,'name':l_name,'email':l_email,'status':l_status,'statusdate':l_statusdate,'desc':l_description,'kw':l_kw,'rel':l_rel,'type':l_type});
			}

			if (l_users.length==18 && i<result.length-1)
			{
				l_nextpage=true;
			}

			if (l_nextpage) break;
		}

		l_s+= $p.network.dashboard.buildSidebar();
		if (l_users.length==0)
		{
			l_s+=lg("noRecommendation");
		}
		else
		{
			l_s+= '<div>'
				+ '<ul class="card-outer">';

			for (var i=0;i<l_users.length;i++)
			{
				var l_options=[l_users[i].rel+" <font color='red'>"+(l_users[i].type=='tag'?lg('commonTags'):lg('commonRelations'))+"</font>",$p.img('ico_friend_add.gif',16,16,'','imgmid')+" <a href='#' onclick='$p.network.card.load("+l_users[i].id+")'>"+lg("add")+"/"+lg("modify")+" </a>"];
				l_s+=$p.network.dashboard.buildElement(l_users[i].id,l_users[i].picture,l_users[i].name,l_users[i].email,l_users[i].status+' -'+$p.date.formatDelai($p.date.delayFromNow($p.date.convertFromDb(l_users[i].statusdate))),l_users[i].desc,l_users[i].kw,l_options,indef,indef);
			}
			l_s+='</ul>'
				+'</div>';
		}
		l_s+='<div style="clear: both;float: none;"></div>';

		var l_page=vars['page']==indef?0:vars['page'].toInt();
		l_s+=$p.html.buildPageNavigator('previous',(l_page==0 ? '' : "$p.network.dashboard.loadRecommended("+(l_page-1)+")"),lg("page")+" "+(l_page+1),'next',(l_nextpage ? "$p.network.dashboard.loadRecommended("+(l_page+1)+")" : ''));

		$p.print('network_content',l_s);
		$p.network.dashboard.centralAreaShown = "USERS";
	}
}
$p.network.add={
	/*
		$p.network.add.search : search user
		inputs : form containing searched string
	*/
	search: function(v_form)
	{
		if ($p.app.user.id == 0) 
		{
			$p.app.alert.show(lg('msgNeedToBeConnectedMenu'));
			v_form.networksearchtxt.value = "";
		}
		else
		{
			var l_email = _lc(v_form.networksearchtxt.value);
			if (l_email.length < 4)
			{
				$p.app.alert.show(lg('4caractMin'));
				return false;
			}
			$p.ajax.call(pep["xmlnetwork_search"] + '?p=0&type='+(checkEmail(l_email)?'m':'t')+'&search='+l_email,
				{
					'type':'load',
					'callback':
					{
						'function':$p.network.add.results
					}
				}
			);
		}
		return false;
	},
	/*
		$p.network.add.results : display searched user results
		inputs : xml response
	*/
	results:function(response,vars)
	{
		var l_result=response.getElementsByTagName("user");
		if (l_result.length==0)
		{
			$p.app.alert.show(lg("noResultForThisSearch"));
		}
		else if (l_result.length==1)
		{
			$p.network.card.load($p.ajax.getVal(l_result[0],"id","int",false,0));
		}
		else
		{
			var l_s=''
				+'<br />'
				+'<div style="'+(l_result.length>10?'overflow:auto;height:100px;width:100%;':'')+'">'
				+'<table>';

			for (var i=0;i<l_result.length;i++)
			{
				l_id=$p.ajax.getVal(l_result[i],"id","int",false,0);
				var l_picture=$p.ajax.getVal(l_result[i],"picture","str",false,"");
				if (l_picture=="") l_picture="nopicture.gif";
				l_s+='<tr>'
					+'<td>'
					+$p.img(l_picture,48,48)
					+'</td>'
					+'<td>'
					+$p.ajax.getVal(l_result[i],"name","str",false,"no name")+'<br />'
					+'<a href="#" onclick="$p.network.card.load('+l_id+')">'+lg('addToMyNetwork')+'</a>'
					+'</td>'
					+'</tr>';
			}
			l_s+='</table>'
				+'</div>'
				+'<br />'
				+'<a href="#" onclick="$p.app.popup.hide()">'+lg('cancel')+'</a>';

			$p.app.popup.show(l_s,500,indef,lg("addPeopleToMyNetwork"));
		}
	}
};

$p.group={
	list:[],
	loaded:false,
	/*
		$p.group.buildPage : Build Chat main page
	*/
	buildPage:function()
	{
		$p.network.init();
		$p.network.buildPageMenu(4);

		var l_s=$p.html.buildTitle(lg('myWorkingGroups'))
			+'<div id="workinggroupsdiv"></div>';
		$p.print('network_content',l_s);
		
		$p.network.dashboard.getUserWorkingGroups();
	},
	buildSidebar: function()
	{
		return '<div class="sidebar">'
			+ $p.html.roundBox('<div class="title">'+lg("options")+' :</div>'
			+ '<div class="content">'
			+ '<a href="#" onclick="$p.network.dashboard.getUserCorporateGroups();return false;">'+lg("myCorporateGroups")+'</a>'
			+ '</div>'
			+ '<div class="title">'+lg("joinAGroup")+' :</div>'
			+ '<div class="content">'
			+ '<form onsubmit="return $p.group.search(this)">'
			+ $p.img('mynetwork.gif',16,16,'','imgmid')+'&nbsp;<input type="text" name="groupsearchtxt" class="thinbox" />'
			+ '&nbsp;<input type="submit" class="submit" value="'+lg("ok")+'" style="width:22px" />'
			+ '</form>'
			+ '</div>'
			+ $p.groupbook.add.getHtml(),
            '#0E679A',
            '260px')
			+ '</div>';
	},
	/*
		$p.group.buildElement : build group card
		inputs :
			v_type : group type (predefined,mygroups)
			v_id : group ID
			v_img : group picture
			v_name : group name
			v_manager : group manager ID
			v_status : group status
			v_options : actions available for this group / user
	*/
	buildElement:function(v_type,v_id,v_img,v_name,v_desc,v_manager,v_status,v_options)
	{
		var l_s= '<li style="list-style-type:none;">'
			+ '<div class="card-inner">'
			+ '<div class="card-content">'
			+ '<table cellpadding="8">'
			+ '<tr>'
			+ '<td valign="top" width="10">'
			+ ((v_img == '' || v_img == indef) ? ''
                                               : '<div class="picture_image_big">'
                                                 + '<img src="'+v_img+'" />'
                                                 + '</div>'
                                                 + '<div class="picture_frame_white_big"> </div>'
            )
			+ '</td>'
			+ '<td valign="top">'
			+ '<div class="card-bar">';
		if (v_type == 'predefined')
		{
            var l_label = lg('membersOfWorkingGroup');
			l_s+= '<a href="#" onclick="$p.network.dashboard.getUsersCorporateGroup('+ v_id +')">'
				+ '<b>'+l_label+  ' ' +  v_name +'</b>'
				+ '</a>';
		}
		else
		{
			l_s+= '<a href="#" onclick=\'$p.notebook.open('+ v_id +',"group","'+v_name+'")\'>';
			if ($p.app.user.id == v_manager) {
				l_s += '<b>'+v_name+'</b>';
			}
			else
			{
				if (v_status == 'I') {
					l_s += '<font color="red">'+v_name+'</font>';
				}
				else
				{
					l_s += '<b>'+v_name+'</b>';
				}
			}
			l_s+= '</a>'
				+ '</div>'
                + ((v_desc == indef || v_desc == '') ? ''
                                                     : $p.string.trunk(v_desc,150)+ '<br />'
                  )
                + '<br />'
				+ v_options
				+ '</td>'
				+ '</tr>'
				+ '</table>'
				+ '</div>'
				+ '</div>'
				+ '</li>';
		}

		return l_s;
	},
	/*
		$p.group.get : get all my groups 
		parameters :
			v_fct (string) - function called when all groups are loaded
	*/
	get:function(v_fct)
	{
		//if already loaded, quit
		if ($p.group.loaded)
		{
			eval(v_fct);
			return;
		}

		$p.ajax.call(pep["xmlnetwork_userworkinggroups"]+'?okOnly=1',
			{
				'type':'load',
				'callback':
				{
					'function':$p.group.storeAndExecute,
					'variables':
					{
						'fct':v_fct
					}
				}
			}
		);
		$p.group.loaded=true;
	},
	/*
		$p.group.storeAndExecute : store all groups for a user and execute function
	*/
	storeAndExecute:function(response,vars)
	{
		var result=response.getElementsByTagName("workinggroup");

		for (var i=0;i<result.length;i++)
		{
			var l_id = $p.ajax.getVal(result[i], "id", "int", false, 0);
			var l_label = $p.ajax.getVal(result[i], "name", "str", false,"...");
			$p.group.list.push({'id':l_id,'label':l_label,'createdBy':$p.ajax.getVal(result[i], "id", "int", false, 0),'status':$p.ajax.getVal(result[i], "status", "str", false, '')});
		}

		//execute function
		eval(vars['fct']);
	},
	/*
		$p.group.reset : reset the user groups list
	*/
	reset:function()
	{
		$p.group.list.length=0;
		$p.group.loaded=false;
	},
	/*
	 * $p.group.getSelected : get all selected groups
	 * */
	getSelected:function(v_form)
	{
	 	var v_tGroup = new Array();
		if(v_form.elements['group[]'] != undefined)
		{
			for(i=0; i<v_form.elements['group[]'].length; i++)
			{
				if(v_form.elements['group[]'][i].checked)
				{
					v_tGroup[v_tGroup.length] = v_form.elements['group[]'][i].id;
				}
			}
		}
		else
		{
			if(v_form.elements['group']!= undefined && v_form.elements['group'].checked)
			{
				v_tGroup[v_tGroup.length] = v_form.elements['group'].id;
			}
		}
		return v_tGroup;
	 },
	 	/*
		$p.group.userIsInGroup : Check if user is in the group
		Parameters:

			v_groupId : group ID concerned
	*/
	userIsInGroup:function(v_groupId)
	{
		return ($p.group.getGroupPosition(v_groupId)==-1 ? false : true);
	},
	/*
		Function: getGroupPosition
			$p.group.getGroupPosition : get the position of the group in the group hash
		Parameters:

			v_groupId : group ID concerned
	*/
	getGroupPosition:function(v_groupId)
	{
		for (var i=0;i<$p.group.list.length;i++)
		{
			if ($p.group.list[i].id==v_groupId) return i;
		}
		return -1;
	},
	/*
		Function: search
			$p.group.search : get the position of the group in the group hash
		Parameters:

			v_groupId : group ID concerned
	*/
	search:function(v_form)
	{
		if (!v_form) return false;

		var l_search=v_form.groupsearchtxt.value;
		if (l_search.length<4)
		{
			$p.app.alert.show(lg('4caractMin'));
			return false;
		}
		$p.ajax.call(pep["xmlnetwork_groupsearch"]+'?p=0&search='+l_search,
			{
				'type':'load',
				'callback':
				{
					'function':$p.group.displaySearchResults
				}
			}
		);

		return false;
	},
	/*
		$p.group.displaySearchResults : display searched group results
		inputs : xml response
	*/
	displaySearchResults:function(response,vars)
	{
		var l_result=response.getElementsByTagName("group");
		if (l_result.length==0)
		{
			$p.app.alert.show(lg("noResultForThisSearch"));
		}
		else
		{
			var l_s=''
				+'<br />'
				+'<ul>';

			for (var i=0;i<l_result.length;i++)
			{
				var l_id=$p.ajax.getVal(l_result[i],"id","int",false,0);
				var l_name=$p.ajax.getVal(l_result[i],"name","str",false,"no name")
				
				l_s+='<li>'
					+'<b>'+l_name+'</b>'
					+' [<a href="#" onclick=\'$p.app.popup.hide();$p.notebook.open('+l_id+',"group","'+l_name+'")\'>'+lg('Notebook')+'</a>]'
					+'<br />'
					+lg('by')+' '+$p.ajax.getVal(l_result[i],"creator","str",false,"??")
					+' | '+$p.date.formatDateLong($p.date.convertFromDb($p.ajax.getVal(l_result[i],"creation_date","str",false,"??")))
					+' | '
					+'<a href="#" onclick=\'$p.group.join('+l_id+',"'+l_name+'")\'>'
					+lg('joinThisGroup')
					+'</a>'
					+'</li>';
			}
			l_s+='</ul>'
				+'<br />'
				+'<a href="#" onclick="$p.app.popup.hide()">'+lg('cancel')+'</a>';

			$p.app.popup.show(l_s,500,indef,lg("joinAGroup"));
		}
	},
    /*
		Function : $p.group.join : join a group
		parameters :
			v_userId : user ID
	*/
	join:function(v_id,v_name)
	{
		$p.ajax.call(pep["scr_groupbook_add"],
			{
				'type':'execute',
				'variables':'act=selectnjoin&id='+v_id+'&name='+v_name,
				'callback':
				{
					'function':$p.group.joinConfirmation,
					'variables':
					{
						'id':v_id,
						'name':v_name
					}
				}
			}
		);
	},
	joinConfirmation:function(response,vars)
	{
		$p.app.popup.hide();
		//$p.notebook.open(vars['id'],'group',vars['name']);
        $p.app.alert.show(lg('groupadded'));
	},
    /*
		Function : $p.group.getUserGroups : get all groups for a user
		parameters :
			v_userId : user ID
	*/
	getUserGroups:function(v_userId)
	{
        var l_s = $p.html.buildTitle(lg('Groups'))
			+'<div id="users'+v_userId+'_workinggroupsdiv"></div>';
		$p.print('notebook'+v_userId+'_content',l_s);
        $p.app.wait('users'+v_userId+'_workinggroupsdiv');

		$p.ajax.call(pep["xmlnetwork_userworkinggroups"]+'?uId='+v_userId+'&allpublic=1',
			{
				'type':'load',
				'callback':
				{
					'function':$p.group.displayUserGroups,
                    'variables':
                    {
                        'userId':v_userId
                    }
				}
			}
		);
	},
	displayUserGroups: function(response, vars)
	{
		var l_s = '<div>'
            + '<ul class="card-outer">';

		var result = response.getElementsByTagName("workinggroup"),l_options = '';

		if (result.length == 0)
		{
			l_s += lg('noGroup');
		}
		else
		{
			for (var i = 0;i < result.length;i++)
			{
				var l_id = $p.ajax.getVal(result[i], "id", "int", false, 0);
				var l_label = $p.ajax.getVal(result[i], "name", "str", false,"...");
                var l_desc = $p.ajax.getVal(result[i], "description", "str", false,"");
				var l_createdBy = $p.ajax.getVal(result[i], "created_by", "int", false, 0);
				var l_status = $p.ajax.getVal(result[i], "status", "str", false, "I");
                var l_picture = $p.ajax.getVal(result[i], "picture", "str", false, '');

                l_options = " <a href='#' onclick=\"$p.group.join("+ l_id +",'"+l_label+"')\">"
                    + lg("joinThisGroup")
                    + '</a>'
                    + (__useNotebook ? " | "
                                       + "<a href='#' onclick=\"$p.notebook.open("+ l_id +",'group','"+l_label+"')\">"
                                       + lg("myGroupbook")
                                       + '</a>'
                                     : '');
                l_s += $p.group.buildElement('mygroup',l_id,(l_picture == '' ? '../images/bigicon_network.gif' : l_picture),l_label,l_desc,l_createdBy,l_status,l_options);
			}
		}
		l_s += '<ul>'
            + '<div style="clear: both;float: none;"></div>'
			+ '</div>';

		$p.print('users'+vars['userId']+'_workinggroupsdiv',l_s);
	},
    /*
		Function : $p.group.callModifyForm : call Group modification form
		parameters :
			v_groupId : group ID
                                v_groupName : group name
	*/
    callModifyForm: function(v_groupId,v_groupName)
    {
        var l_s = '<div id="groupmodifyform"></div>';
        $p.app.popup.fadein(l_s,500,indef,v_groupName,true,'$p.group.loadModifyForm('+v_groupId+')');
    },
    /*
		Function : $p.group.loadModifyForm : load data for the modification form
		parameters :
			v_groupId : group ID
	*/
    loadModifyForm: function(v_groupId)
    {
        $p.ajax.call(pep["xml_groupbook_properties"]+'?id='+v_groupId,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.group.modifyForm,
                    'variables':
                    {
                        'groupId':v_groupId
                    }
                }
            }
        );
    },
    /*
		Function : $p.group.modifyForm : display modification form
	*/
    modifyForm: function(response,vars)
    {
        var l_result = response.getElementsByTagName('groupbook');
        var l_private = $p.ajax.getVal(l_result[0],'private','str','');
        var l_picture = $p.ajax.getVal(l_result[0],'picture','str','');
        var l_title = $p.ajax.getVal(l_result[0],'name','str','');
        var l_description = $p.ajax.getVal(l_result[0],'description','str','');

        var l_s = '<form name="groupmodifyform" onsubmit="$p.group.modify(this);return false;">'
            + '<input type="hidden" name="id" value="'+vars['groupId']+'" />'
            + '<input type="hidden" name="picture" value="'+l_picture+'" />'
            + '<table>'
            + '<tr>'
            + '<td width="100">'+lg('lblTitle')+'</td>'
            + '<td><input type="text" name="groupname" style="width: 350px;" value="'+l_title+'" /></td>'
            + '</tr>'
            + '<tr>'
            + '<td>'
            + '<img class="picture" width="64" id="groupmodifyformpicture" src="'+(l_picture == '' ? '../images/bigicon_network.gif' : l_picture)+'" />'
            + '</td>'
            + '<td><iframe src="../includes/upload_component.inc.php?subfolder=profile&fct=$p.group.updateModifyFormPicture&closeafter=no&type=image&w=128&h=128" width="350" height="60" frameborder="0"></iframe></td>'
            + '</tr>'
            + '<tr>'
            + '<tr>'
            + '<td>'+lg('access')+'</td>'
            + '<td><input type="radio" name="access" value="1"'+(l_private == 1 ? ' checked="checked"' : '')+' />'+lg('privateMemberArticle')+' <input type="radio" name="access" value="0"'+(l_private == 0 ? ' checked="checked"' : '')+' />'+lg('public')+' </td>'
            + '</tr>'
            + '<tr>'
            + '<td valign="top">'+lg('description')+'</td>'
            + '<td><textarea name="groupdescription" rows="6" style="width: 350px;">'+l_description+'</textarea></td>'
            + '</tr>'
             + '<tr>'
            + '<td></td>'
            + '<td><input type="submit" value="'+lg('submit')+'" /></td>'
            + '</tr>'
            + '</table>';
        $p.print('groupmodifyform',l_s);
    },
    /*
		Function : $p.group.modify : modify a group
	*/
    modify: function(v_form)
    {
        $p.ajax.call(pep["scr_group_update"],
            {
                'type':'execute',
                'variables':'id='+v_form.id.value
                            + '&title='+$p.string.esc(v_form.groupname.value)
                            + '&picture='+$p.string.esc(v_form.picture.value)
                            + '&access='+$p.navigator.getRadioValue(v_form.access)
                            + '&description='+$p.string.esc(v_form.groupdescription.value),
                'callback':
                {
                    'function':$p.group.modifyConfirmation,
                    'variables':
                    {
                        'id':v_form.id.value,
                        'name':v_form.groupname.value
                    }
                }
            }
        );
    },
    modifyConfirmation: function(vars)
    {
        $p.app.popup.hide();
        $p.app.alert.show(lg('modificationApplied'));
        //rename group in page and tab
        $p.groupbook.build(vars['id'],vars['name']);
        tab[$p.app.tabs.sel].name = vars['name'];
    },
    updateModifyFormPicture: function(v_type,v_origFile,v_newFile,v_size)
    {
        if (v_type == 'add')
		{
            var l_file = '../upload/profile/'+v_newFile;
            document.forms['groupmodifyform'].picture.value = l_file;
            $('groupmodifyformpicture').src = l_file;
		}
    },
    /*
		$p.group.loadActivity : load activity of my groups
                      inputs :
			v_nb : number of items to display
			v_page : current page number
	*/
    loadActivity: function(v_nb,v_page,v_div)
    {
        //refresh menu
        $p.network.information.displaySummaryMenu('groups');
        
        $p.ajax.call(pep["xmlnetwork_summary"]+'?type=group&nb='+(v_nb+1)+'&p='+v_page,
			{
				'type':'load',
				'callback':
				{
					'function':$p.group.displayActivity,
					'variables':
					{
						'nb':v_nb,
						'page':v_page,
                        'divid':v_div
					}
				}
			}
		);
    },
    displayActivity: function(response,vars)
	{
		var l_s = '<div>';

		var l_result = response.getElementsByTagName("update");
		if (l_result.length > 0)
		{
			var l_previousDate,
                l_currentDate;

			for (var i = 0;i < _min(l_result.length,vars['nb']);i ++)
			{
				var l_date = $p.ajax.getVal(l_result[i],"pubdate","str",false,"");
				l_currentDate = $p.date.formatDateLong($p.date.convertFromDb(l_date));
				if (l_previousDate != l_currentDate)
				{
					//l_s+="<div class='subtitle' style='clear: left;margin: 6px 0 4px 0;'>"+l_currentDate+"</div>";
					l_s += "<div class='subtitle' style='clear: left;margin: 6px 0 4px 0;'>"
                        + l_currentDate
                        + "</div>";
					l_previousDate = l_currentDate;
				}
				var l_picture = $p.ajax.getVal(l_result[i],"picture","str",false,""),
                    l_id = $p.ajax.getVal(l_result[i],"userid","int",false,0),
                    l_type = $p.ajax.getVal(l_result[i],"type","int",false,2),
                    l_link = $p.ajax.getVal(l_result[i],"link","str",false,""),
                    l_title = $p.ajax.getVal(l_result[i],"title","str",false,"..."),
                    l_groupName = $p.ajax.getVal(l_result[i],"name","str",false,"...");
                //apply default picture
                if (l_picture == "") l_picture = "../images/nopicture.gif";

				l_s += $p.network.information.buildItem(l_id,l_type,l_link,l_title,l_picture,$p.ajax.getVal(l_result[i],"long_name","str",false,"???"),l_date,lg('lblGroup')+' : '+l_groupName);
			}
			l_s+="<div style='clear: both;float: none;'></div>"
				+ $p.html.buildPageNavigator('previous',
                                            (vars['page'] == 0 ? '' 
                                                             : '$p.group.loadActivity('+vars['nb']+','+(vars['page']-1)+',"'+vars['divid']+'");return false;'),
                                            '',
                                            'next',
                                            (l_result.length <= vars['nb'] ? '' : '$p.group.loadActivity('+vars['nb']+','+(vars['page']+1)+',"'+vars['divid']+'");return false;')
                 )
				+'</div>';

		}
		else
		{
			l_s+="<b>"+lg("noNewsOfYourNetwork")+"</b>";
			l_s+="</ul><div style='clear: both;float: none;'>";
			l_s+="<div style='text-align: center;background: #c6c3c6;height: 22px;margin-top: 15px;padding-top: 3px;'></div>";
		}
        l_s += '</div>';

		$p.print(vars['divid'],l_s);
	}
}
$p.group.card={
	/*
		$p.group.card.load : load user information
		inputs :
			v_id : user ID
			v_loggedId : user logged ID
			v_add : true=the user is not yet in my network, false=the user is in my network
	*/
	load:function(v_id,v_loggedId,v_add)
	{
		v_add= false;
		$p.ajax.call(pep["xmlnetwork_userdetail"]+'?id='+v_id,
			{
				'type':'load',
				'callback':
				{
					'function':$p.group.card.show,
					'variables':
					{
						'id':v_id,
						'logid':v_loggedId,
						'add':v_add
					}
				}
			}
		);
	},
	/*
		$p.group.card.show : display user information in popup
		inputs : xml response
			vars :
				[1] : user Id
				[2] : logged user Id
				[3] : add boolean value
	*/
	show: function(response,vars)
	{
		var l_s = '';
		var l_name = $p.ajax.getVal(response,"longname","str",false,"no name");
		var l_picture = $p.ajax.getVal(response,"picture","str",false,"");
		if (vars['logid'] == indef)
            var l_add = $p.ajax.getVal(response,"new","int",false,1);

		if (l_picture == "") l_picture = "nopicture.gif";
		l_s += '<br />'
			+ '<table>'
			+ '<tr>'
			+ '<td valign="top">'+$p.img(l_picture,64,64,'','picture')+'</td>'
			+ '<td valign="top">'
			+ $p.ajax.getVal(response,'username','str',false,'')
			+ '<br /><br />'
			+ '<a href="#" onclick=\'$p.notebook.open('+vars['id']+',"note","'+l_name+'",indef,indef,"'+l_picture+'")\'>'
			+ $p.img("ico_notebook.gif",16,16,"","imgmid")+' '+lg("seeHisNotebook")
			+ '</a>'
			+ '</td>'
			+ '</tr>'
			+ '</table>';

		//get user keywords
		var l_kw = [],l_result = response.getElementsByTagName("keyword");
		for (var i = 0;i < l_result.length;i++)
		{
			l_kw.push(l_result[i].firstChild.nodeValue);
        }
    
        var l_group = $p.ajax.getVal(response,"group","int",false,0);
        if (l_group > 0) {
    		l_s += '<br />'+lg('selectGroupsInWhichYouInvite')+' '+l_name+':'
				+ '<br /><form name="frmLstGrp" onsubmit="return $p.group.card.save(this);">'
				+ '<div id="sharegroups"></div>'
				+ '<center><input type="hidden" name="uid" value="'+vars['id']+'" />'
				+ '<input type="submit" id="btnAdd" value="'+lg('add')+'" class="submit" /> '
				+ '<input type="button" class="btn" onclick="$p.app.popup.hide()" value="'+lg('cancel')+'" />'
				+ '<br/>'
				+ '</form>'
				+ '</center>'
				+ '<br />';
            $p.friends.loadGroups('btnAdd', vars['id']);
        }
        else
		{
            l_s += '<br />'
				+ lg('noGroupRightsRequired')+'<br /><br />'
				+ '<center>'
				+ '<input type="button" class="btn" onclick="$p.app.popup.hide()" value="'+lg('cancel')+'" />'
				+ '</center>';
        }
	
		var l_t = lg("Invite")+" " + l_name+" "+lg("inGroups");

		if (_gel("popupcontent") == null)
		{
			$p.app.popup.show(l_s,500,indef,l_t);
		}
		else
		{
			$p.print('popupcontent',l_t);
		}
	},
	/*
		$p.group.card.save : save user in a group 
		inputs :
			v_form : form containing updated information
	*/
	save:function(v_form)
	{
		var l_vars=[];
		var j = 0;
		var v_tGroup = $p.group.getSelected(v_form);
		
		for(i=0; i<v_tGroup.length; i++)
		{
			l_vars.push("gId"+j+"="+v_tGroup[i]);
			j++;
		}

		if (j > 0)
		{
			$p.ajax.call(pep["scr_group_adduser"],
				{
					'type':'execute',
					'variables':'id='+v_form.uid.value+'&'+l_vars.join("&"),
					'alarm':false,
					'forceExecution':false,
					'callback':
					{
						'function':$p.group.card.addConfirmation
					}
				}
			);

		}
		else 	$p.app.alert.show(lg("youHaveToSelectOneGroup"), 1);
		
		return false;
	},
	/*
		$p.group.card.addConfirmation : send confirmation that user has been added in groups 
	*/
	addConfirmation:function()
	{
		$p.app.alert.show(lg('userAddedInGroups'));
		$p.app.popup.hide();
	}
}
$p.network.card={
	/*
		$p.network.card.load : load user information
		inputs :
			v_id : user ID
			v_add : true=the user is not yet in my network, false=the user is in my network
	*/
	load:function(v_id,v_add)
	{
       $p.ajax.call(pep["xmlnetwork_userdetail"]+'?id='+v_id,
			{
				'type':'load',
				'callback':
				{
					'function':$p.network.card.show,
					'variables':
					{
						'id':v_id,
						'add':v_add
					}
				}
			}
		);
	},
	/*
		$p.network.card.show : display user information in popup
		inputs : xml response
	*/
	show: function(response,vars)
	{
		var l_s = "",
            l_name = $p.ajax.getVal(response,"longname","str",false,"no name"),
            l_add = (vars['add']==indef?$p.ajax.getVal(response,"new","int",false,1):vars['add']),
            l_picture = $p.ajax.getVal(response,"picture","str",false,""),
            l_username = $p.ajax.getVal(response,"username","str",false,"");

		if (l_picture == "")
            l_picture = "../images/nopicture.gif";

		l_s += (l_add ? '' : '<center><b>'+lg('peopleAlreadyInYourNetwork')+'</b></center>')
            + '<br />'
			+ '<table>'
			+ '<tr>'
			+ '<td valign="top" width="100">'
            + '<div class="picture_image_big">'
            + '<img src="'+l_picture+'" />'
            + '</div>'
            + '<div class="picture_frame_white_big"> </div>'
			+ '</td>'
			+ '<td valign="top">'
			+ '<h2>'+l_name+'</h2>'
            + '<br />'
            + '<a href="#" onclick=\'$p.notebook.open('+vars['id']+',"note","'+l_name+'",1);$p.app.popup.hide();\'>'+lg('profil')+'</a>'
			+ (__useNotebook ? ' | <a href="#" onclick=\'$p.notebook.open('+vars['id']+',"note","'+l_name+'",indef,indef,"'+l_picture+'");$p.app.popup.hide();\'>'+$p.img("ico_notebook.gif",16,16,"","imgmid")+" "+lg("seeHisNotebook")+"</a>" : "")
			+ '</td>'
			+ '</table>';

		//get user keywords
		var l_kw = [],l_result=response.getElementsByTagName("keyword");
		for (var i = 0;i < l_result.length;i ++)
		{
			l_kw.push(l_result[i].firstChild.nodeValue);
		}
		
		l_s += "<br />"
            + "<div class='title'>"+lg('describeThisPerson')+"</div><br />"
            + "<form onsubmit='return $p.network.card.save(this,"+l_add+")'>"
            + "<input type='hidden' name='uid' value='"+vars['id']+"' />"
			+ lg("tags")+" "+tooltip("helpTagsPeople")+"<br />"
            + "<input type='text' id='networktagsinput' name='keywords' maxlength='250' value=\""+l_kw.join(",")+"\" onkeyup=\"$p.tags.autocompletion.get('networktagsinput')\" onblur='$p.tags.autocompletion.hide()' style='width: 470px' /><br /><br />"
			+ lg("description")+"<br />"
            + "<textarea name='description' style='width: 470px' rows='6'>"+$p.ajax.getVal(response,"description","str",false,"")+"</textarea><br /><br />"
			+ "<center><input type='submit' value='"+(l_add==1?lg("addToMyNetwork"):lg("modify"))+"' class='submit' /> <input type='button' class='btn' onclick='$p.app.popup.hide()' value='"+lg("cancel")+"' /></center>"
			+ "</form><br />";

		if (_gel("popupcontent") == null)
		{
			$p.app.popup.show(l_s,600,indef,l_name);
		}
		else
		{
			$p.print("popupcontent",l_s);
		}              
	},
	/*
		$p.network.card.save : save network user changes
		inputs :
			v_form : form containing updated information
			v_add : is the user new or not in my network (1=new)
	*/	
	save:function(v_form,v_add)
	{   
		var l_keywords=$p.tags.formatList(v_form.keywords.value);
		$p.ajax.call(pep["scr_network_adduser"],
			{
				'type':'execute',
				'variables':'act='+(v_add==1?'add':'upd')+'&id='+v_form.uid.value+'&kw='+l_keywords+'&kwformated='+$p.string.formatForSearch(l_keywords)+'&desc='+v_form.description.value,
				'alarm':false,
				'forceExecution':false,
				'callback':
				{
					'function':((v_add==1)?$p.network.card.addSuccess:$p.network.card.close)
				}
			}
		);
		return false;
	},
	/*
		$p.network.card.addSuccess : user has been added successfully in network
		inputs :
			v_form : form containing updated information
			v_add : is the user new or not in my network
	*/
	addSuccess:function()
	{
		$p.network.dashboard.nbUsersInMyNetwork++;
		$p.app.alert.show(lg("peopleAddedToNetwork"));
		$p.network.card.close();
	},
	/*
		$p.network.card.close : close user card
		inputs : user ID
	*/
	close:function(v_id)
	{
        $p.app.popup.hide();
		if ($p.plugin.page=='mynetwork')$p.network.dashboard.refresh();
	}
}

/*
	$p.network.suppress : suppress a user from my network
	inputs : user ID
*/
$p.network.suppress=function(v_id)
{
	var l_input=confirm(lg("confirmUserRemove"));
	if (l_input==1)
	{
		$p.ajax.call(pep["scr_network_removeuser"],
			{
				'type':'execute',
				'variables':'id='+v_id,
				'alarm':false,
				'forceExecution':false,
				'callback':
				{
					'function':$p.network.dashboard.refresh
				}
			}
		);
		$p.network.dashboard.nbUsersInMyNetwork--;
	}
	return false;
}
$p.network.information={
	/*
		$p.network.information.summary : init NEWS FEED
		inputs : container for summary information
	*/
	summary: function(v_div)
	{
		if (v_div == indef)
            v_div = 'plugindiv';

		var l_s = '<div id="homenewsofmynetworkmenu"></div>'
			+ '<div id="homenewsofmynetwork"></div>';

		$p.print(v_div,l_s);
		$p.network.information.summaryLoadNetwork(8,0,'homenewsofmynetwork');
	},
    displaySummaryMenu: function(v_type)
    {
        var l_s =  $p.html.buildTitle(lg('myNetwork'),
            (v_type == 'network' ? '' : '<a href="#" onclick=\'$p.network.information.summaryLoadNetwork(8,0,"homenewsofmynetwork");return false;\'>')
            + lg('myNetwork')
            + (v_type == 'network' ? '' : '</a>')
            + ' | '
            + (v_type == 'groups' ? '' : '<a href="#" onclick=\'$p.group.loadActivity(8,0,"homenewsofmynetwork");return false;\'>')
            + lg('myGroups')
            + (v_type == 'groups' ? '' : '</a>')
            );

        $p.print('homenewsofmynetworkmenu',l_s);
    },
	/*
		$p.network.information.menu : information of my network menu
	*/
	menu:function()
	{
		$p.app.menu.addTitle('networkinfomenu_1','',lg("myNetwork"));
		$p.app.menu.addArea('networkinfomenu_1',$p.img('puce.gif')+'&nbsp;<a href="#" onclick="$p.network.information.display();return false;">'+lg('latestNews')+'</a><br />');

		$p.app.menu.openSubMenu('networkinfomenu_1',true);
	},
	open:function()
	{
		if ($p.app.env!='info'){    $p.network.init(lg('myInfo'),'info');   }
		$p.plugin.content("<div id='plugindiv'></div>");
	},
	/*
		$p.network.information.summaryLoad : load my profile
	*/
	summaryLoad:function()
	{
		$p.ajax.call(pep["xmlnetwork_myprofile"]+'?id=1',
			{
				'type':'load',
				'callback':
				{
					'function':$p.network.information.profile
				}
			}
		);
	},
	/*
		$p.network.information.profile : display my profile
		inputs : xml response
	*/
	profile:function(response,vars)
	{
		var l_picture=$p.ajax.getVal(response,"picture","str",false,"");
		$p.print("mypicture","<img src='"+(l_picture==""?"../images/nopicture.gif":l_picture)+"' width='36' height='36' class='picture' /></td>");
		if (document.forms["stats"])
		{
			var l_status=$p.ajax.getVal(response,"stat","str",false,"");
			if (l_status=="") l_status=lg("myStatus")+" ...";
			document.forms["stats"].stat.value=l_status;
		}
		$p.network.information.summaryLoadNetwork();
	},
	/*
		$p.network.information.display : load my network last news
	*/
	display:function()
	{
		$p.network.init();

		$p.print('network_content',$p.html.buildTitle(lg('newOfYourNetwork'))+'<div id="newsofyournetwork"></div>');
		$p.app.wait('newsofyournetwork');
		
		if ($p.app.user.id==0)
		{
			$p.print('newsofyournetwork',lg('msgNeedToBeConnectedPage'));
		}
		else
		{
			$p.network.information.summaryLoadNetwork(40,0,'newsofyournetwork');
		}
	},
	/*
		$p.network.information.summaryLoadNetwork : load network NEWS FEED
	*/
	summaryLoadNetwork:function(v_nb,v_page,v_div)
	{
		if (v_nb==indef) v_nb=40;
		if (v_page==indef) v_page=0;

        //display menu
        $p.network.information.displaySummaryMenu('network');

		$p.ajax.call(pep["xmlnetwork_summary"]+'?type=network&nb='+(v_nb+1)+'&p='+v_page,
			{
				'type':'load',
				'callback':
				{
					'function':$p.network.information.summaryDisplay,
					'variables':
					{
						'nb':v_nb,
						'page':v_page,
						'divid':v_div
					}
				}
			}
		);
	},
	/*
		$p.network.information.summaryDisplay : display NEWS FEED
		inputs : xml response
	*/
	summaryDisplay: function(response,vars)
	{
		var l_s = '<div>';

		var l_result = response.getElementsByTagName("update");
		if (l_result.length > 0)
		{
			var l_previousDate,
                l_currentDate;

			for (var i = 0;i < _min(l_result.length,vars['nb']);i ++)
			{
				var l_date = $p.ajax.getVal(l_result[i],"pubdate","str",false,"");
				l_currentDate = $p.date.formatDateLong($p.date.convertFromDb(l_date));
				if (l_previousDate != l_currentDate)
				{
					//l_s+="<div class='subtitle' style='clear: left;margin: 6px 0 4px 0;'>"+l_currentDate+"</div>";
					l_s += "<div class='subtitle' style='clear: left;margin: 6px 0 4px 0;'>"
                        + l_currentDate
                        + "</div>";
					l_previousDate = l_currentDate;
				}
				var l_picture = $p.ajax.getVal(l_result[i],"picture","str",false,"");
				if (l_picture == "") l_picture = "../images/nopicture.gif";
				var l_id = $p.ajax.getVal(l_result[i],"userid","int",false,0);
				var l_type = $p.ajax.getVal(l_result[i],"type","int",false,2);
				var l_link = $p.ajax.getVal(l_result[i],"link","str",false,"");

				l_s += '<div class="homeitem">'
                    + $p.network.information.buildItem(l_id,l_type,l_link,$p.ajax.getVal(l_result[i],"title","str",false,"..."),l_picture,$p.ajax.getVal(l_result[i],"long_name","str",false,"???"),l_date)
                    + '</div>';
			}
			l_s+="<div style='clear: both;float: none;'></div>"
				+ $p.html.buildPageNavigator('previous',
                                            (vars['page'] == 0 ? '' 
                                                             : '$p.network.information.summaryLoadNetwork('+vars['nb']+','+(vars['page']-1)+',"'+vars['divid']+'");return false;'),
                                            '',
                                            'next',
                                            (l_result.length <= vars['nb'] ? '' : '$p.network.information.summaryLoadNetwork('+vars['nb']+','+(vars['page']+1)+',"'+vars['divid']+'");return false;')
                 )
				+'</div>';

		}
		else
		{
			l_s+="<b>"+lg("noNewsOfYourNetwork")+"</b>";
			l_s+="</ul><div style='clear: both;float: none;'>";
			l_s+="<div style='text-align: center;background: #c6c3c6;height: 22px;margin-top: 15px;padding-top: 3px;'></div>";
		}
        l_s += '</div>';
		$p.print(vars['divid'],l_s);
	},
	myNews:function()
	{
		$p.network.init();
		$p.plugin.content($p.html.buildTitle(lg('myContributions'))+'<div id="mynewsdiv"></div>');

		$p.ajax.call(pep["xmlnetwork_usersummary"],
			{
				'type':'load',
				'callback':
				{
					'function':$p.network.information.myNewsDisplay
				}
			}
		);
		return false;
	},
	myNewsDisplay:function(response,vars)
	{
		var l_s=lg('myContributionDesc')+'<br /><br />';
		var l_result=response.getElementsByTagName("update"),l_date,l_prevdate;
		if (l_result.length>0)
		{
			for (var i=0;i<l_result.length;i++)
			{
				var l_id=$p.ajax.getVal(l_result[i],"id","int",false,0);
				var l_type=$p.ajax.getVal(l_result[i],"type","int",false,2);
				var l_link=$p.ajax.getVal(l_result[i],"link","str",false,"");
				var l_date=$p.ajax.getVal(l_result[i],"pubdate","str",false,"");
				var l_formatedDate=$p.date.formatDateLong($p.date.convertFromDb(l_date),false);
				if (l_formatedDate!=l_prevdate) l_s+="<div class='subtitle'>"+l_date+" :</div>";
				l_prevdate=l_date;
				$p.network.information.buildItem(l_id,l_type,l_link,$p.ajax.getVal(l_result[i],"title","str",false,"..."),'',$p.app.user.name,l_date,'<a href="#" onclick="$p.network.information.suppress('+l_id+')" title="'+lg('stopSharing')+'">'+lg('suppress')+'</a>');

			}
		} else 
		{
			l_s+="<b>"+lg("noNews")+"</b>";
			l_s+="</ul><div style='clear: both;float: none;'>";
			l_s+="<div style='text-align: center;background: #c6c3c6;height: 22px;margin-top: 15px;padding-top: 3px;'></div>";
		}
		
		$p.print("mynewsdiv",l_s);
	},
	/*
		$p.network.information.buildItem : build a news
		input : 
			v_type : news type
			v_link : news link
			v_title : news title
			v_picture : news picture
			v_name : news owner
			v_date : news date
			v_options : options displayed
	*/
	buildItem: function(v_id,v_type,v_link,v_title,v_picture,v_name,v_date,v_options)
	{
		var l_function = '',
            l_s = '<div style="clear: both;float: none;">';

        v_link = $p.url.relativeToAbsolute(v_link);
		
		if (v_picture == indef)
		{
			l_s += '<a href="#" onclick=\'$p.notebook.open('+v_id+',"note","'+v_name+'")\'>'
				+ v_name
				+ '</a> ';
			v_picture = "";
		}
		else
		{
			l_s += '<div style="margin: 2px;float: left;width: 40px;height: 40px;line-height: 40px;">'
				+ '<a href="#" onclick=\'$p.notebook.open('+v_id+',"note","'+v_name+'",indef,indef,"'+v_picture+'")\'>'
				+ (v_picture=='' ? '' 
                                : '<div class="picture_image_medium">'
                                  + '<img src="'+v_picture+'" />'
                                  + '</div>'
                                  + '<div class="picture_frame_white_medium"> </div>'
                )
				+ '</a>'
				+ '</div>'
				+ '<a href="#" onclick=\'$p.notebook.open('+v_id+',"note","'+v_name+'",indef,indef,"'+v_picture+'")\'>'
				+ v_name
				+ '</a> ';
		}
		
		switch (v_type)
		{
			case 1:
				//article writen
				l_function = '$p.notebook.open('+$p.string.getVar(v_link,'id')+',"note","'+v_name+'",indef,"'+$p.string.getVar(v_link,'artid')+'","'+v_picture+'")';
				break;
			case 2:
				//article shared
				l_function = '$p.notebook.open('+$p.string.getVar(v_link,'id')+',"note","'+v_name+'",indef,"'+$p.string.getVar(v_link,'artid')+'","'+v_picture+'")';
				break;
			case 3:
				//widget shared
				l_function = '$p.app.widgets.open('+$p.string.getVar(v_link,'pid')+',"'+v_link+'","uniq")'
				break;
			case 4:
				//page shared
				l_function = '$p.app.pages.loadSharedPortal('+$p.string.getVar(v_link,'id')+',1,indef,true)'
				break;
			case 5:
				//comment
				l_function = '$p.notebook.open('+$p.string.getVar(v_link,'id')+',"note","'+v_name+'",indef,"'+$p.string.getVar(v_link,'artid')+'","'+v_picture+'","comments")';
				break;
			case 7:
				//trackback
				l_function = '$p.notebook.open('+$p.string.getVar(v_link,'id')+',"note","'+v_name+'",indef,"'+$p.string.getVar(v_link,'artid')+'","'+v_picture+'","trackbacks")';
				break;
            case 9:
                //groupbook article
                l_function = '$p.notebook.open('+$p.string.getVar(v_link,'id')+',"group","'+v_name+'",indef,"'+$p.string.getVar(v_link,'artid')+'")';
				break;
		}
		l_s += lg("txtNetSummary"+v_type)+' '
			+ (v_link=='' ? '' : ($p.url.ishttp(v_link) ? '<a href="'+v_link+'" target="_blank">' : '<a href="#" onclick=\''+l_function+'\'>'))
			+ v_title
			+ (v_link=='' ? '' : '</a>')
            + '<br />'
            + (v_options==indef ? '' : v_options+' &nbsp;')
			+ (v_date==indef ? '' : '<span style="color: #aaa;font-size: 0.9em;">'+lg('at')+' '+$p.date.getTime($p.date.convertFromDb(v_date))+'</span>')
			+ '</div>';

		return l_s;
	},
	/*
		$p.network.information.suppress : suppress a network news
		input : 
			v_id : id of the news to suppress
	*/
	suppress:function(v_id)
	{
		l_input=confirm(lg("areYouSureToSuppressNews"));
		if (l_input==1)
		{
			$p.ajax.call(pep["scr_supnews"],
				{
					'type':'execute',
					'variables':'id='+v_id,
					'alarm':true,
					'forceExecution':true,
					'callback':
					{
						'function':$p.network.information.myNews
					}
				}
			);
		}	
	}
};

$p.groupbook={

	open: function(v_groupId)
	{
		$p.notebook.open(v_groupId,'group');
	},
	/*
		$p.groupbook.build : build a group notebook
		Parameters:

			v_groupId : group ID
			v_title : notebook title
			v_picture : notebook picture
			v_option : tab of the groupbook selected by default
	*/
	build: function(v_groupId,v_title,v_picture,v_option,v_articleid,v_anchor)
	{
		var l_s = '<div class="feature">'
            + '<div id="groupbook'+v_groupId+'_header"></div>'
			+ '<div id="groupbook'+v_groupId+'_content" class="content"></div>'
			+ '</div>';
		$p.print('modules'+tab[$p.app.tabs.sel].id,l_s);

        $p.groupbook.loadProperties(v_groupId);

		$p.groupbook.selectMenuOption(v_groupId,v_option,{'articleid':v_articleid,'anchor':v_anchor});
	},
    	/*
		$p.groupbook.loadProperties : build a group notebook header
		Parameters:

			v_groupId : group ID
	*/
    loadProperties: function(v_groupId)
    {
        $p.ajax.call(pep["xml_groupbook_properties"]+'?id='+v_groupId,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.groupbook.displayProperties,
                    'variables':
                    {
                        'groupId':v_groupId
                    }
                },
                'asynchron':false
            }
        );
    },
    displayProperties: function(response,vars)
    {
        var l_result = response.getElementsByTagName('groupbook');
        var isMember = $p.ajax.getVal(l_result[0],'ismember','str','');
        var l_private = $p.ajax.getVal(l_result[0],'private','str','');
        var l_picture = $p.ajax.getVal(l_result[0],'picture','str','');
        var l_createdby = $p.ajax.getVal(l_result[0],'createdby','int',0);
        var l_title = $p.ajax.getVal(l_result[0],'name','str','');

        var l_s = $p.html.buildFeatureHeader({
            'image':(l_picture == '' ? '../images/bigicon_network.gif' : l_picture),
            'title':l_title+' '
                    + ((l_private == '0' && isMember == '') ? '<span style="font-size: 11px"><a href="#" onclick=\'$p.group.join('+vars['groupId']+',"'+l_title+'");return false;\'>'+$p.img('ico_friend_add.gif',16,16,'','imgmid')+' '+lg('joinThisGroup')+'</a></span> ' : '')
                    + (l_createdby == $p.app.user.id ? '<span style="font-size: 11px"><a href="#" onclick=\'$p.group.callModifyForm('+vars['groupId']+',"'+l_title+'");return false;\'>'+lg('modify')+'</a></span> ' : ''),
            'menu':'<div id="groupbook'+vars['groupId']+'_menu"></div>'
        });
        $p.print('groupbook'+vars['groupId']+'_header',l_s);
    },
	/*
		$p.groupbook.buildMenu : build a group notebook menu
		Parameters:

			v_groupId : group ID
	*/
	buildMenu:function(v_groupId,v_selOption)
	{
		var l_h=[];
		l_h.push({'id':1,'fct':'$p.groupbook.selectMenuOption('+v_groupId+',1);','icon':'mynetwork.gif','label':lg('authors')});
		l_h.push({'id':2,'fct':'$p.groupbook.selectMenuOption('+v_groupId+',2);','icon':'ico_notebook.gif','label':lg('publications')});
		if ($p.group.userIsInGroup(v_groupId))
		{
			l_h.push({'id':5,'fct':'$p.groupbook.selectMenuOption('+v_groupId+',5);','icon':'ico_write.gif','label':lg('writeArticle')});
		}

		$p.print('groupbook'+v_groupId+'_menu',$p.html.buildFeatureMenu(v_selOption,l_h));
	},
	/*
		$p.groupbook.selectMenuOption : select a menu option
		Parameters:

			v_groupId : group ID
			v_selOption : option selected
	*/
	selectMenuOption:function(v_groupId,v_selOption,v_extra)
	{
		if (v_selOption==indef) v_selOption=2;

		$p.group.get('$p.groupbook.buildMenu('+v_groupId+','+v_selOption+')');

		switch(v_selOption)
		{
			case 1:
				$p.groupbook.getAuthors(v_groupId);
				break;
            case 2:
				if (v_extra==indef || v_extra['articleid']==indef)
				{
					$p.groupbook.articles.get(v_groupId);
				}
				else
				{
					$p.notebook.articles.getDetail(v_groupId,(v_extra==indef ? indef : v_extra['articleid']),0,'group',(v_extra==indef ? indef : v_extra['anchor']));
				}
				break;
			case 5:
				$p.notebook.articles.write(v_groupId,'group');
				break;
		}
	},
	/*
		$p.groupbook.getAuthors : get the group book authors list
		Parameters:

			v_groupId : group ID
			v_selOption : option selected
	*/
	getAuthors:function(v_groupId)
	{
		$p.print('groupbook'+v_groupId+'_content','<div id="groupbook'+v_groupId+'_authorslist"></div>');

		$p.ajax.call(pep["xmlnetwork_usersnotebookgroup"]+'?gid='+v_groupId,
			{
				'type':'load',
				'callback':
				{
					'function':$p.network.dashboard.displayUsersWorkingGroup,
					'variables':
					{
						'v0':'',
						'v1':true,
						'v2':true,
						'showRemoveButton':true,
						'showAddBtn':false,
						'title':lg("membersOfGroupname"),
						'divid':'groupbook'+v_groupId+'_authorslist'
					}
				}
			}
		);
	}
}
$p.groupbook.articles={
/*
		$p.groupbook.articles.get : Get group notebook articles
		Parameters:

			v_groupId : group ID
			v_page : page number
			v_search : search text (string)
	*/
	get:function(v_groupId,v_page,v_search)
	{
		if (v_page==indef) v_page=0;
		$p.app.wait('groupbook'+v_groupId+'_content');
		$p.ajax.call(pep["notebook_xmlgrouparticles"]+'?id='+v_groupId+'&page='+v_page+(v_search==indef ? '' : '&search='+v_search+'&type=plaintxt'),
			{
				'type':'load',
				'callback':
				{
					'function':$p.groupbook.articles.display,
					'variables':
					{
						'groupId':v_groupId,
						'page':v_page,
						'search':v_search
					}
				}
			}
		);
	},
	/*
		$p.groupbook.articles.display : Display group notebook articles
		Parameters:

			response : XML response
			vars : variables sent
	*/
	display:function(response,vars)
	{
		var l_s = $p.html.buildTitle(lg('publications'),'<form onsubmit="$p.groupbook.articles.get('+vars['groupId']+',0,this.search.value);return false;" style="padding: 0px;margin: 0px;"><input type="text" name="search" value="'+(vars['search']==indef ? '' : vars['search'])+'" /> <input class="thinbox" type="submit" value="'+lg('ok')+'" /></form>')
			+ '<div style="width: 100%;text-align: right;">'
            + '<a href="#" onclick=\'$p.groupbook.articles.get('+vars['groupId']+','+vars['page']+(vars['search']==indef ? '' : ',"'+vars['search']+'"')+');return false;\'>'+$p.img('ico_refresh2.gif',16,16,'','imgmid')+' '+lg('lblRefresh')+'</a> | '
			+ ($p.group.userIsInGroup(vars['groupId']) ? '<a href="#" onclick=\'$p.notebook.articles.write('+vars['groupId']+',"group");\'>'+$p.img('ico_write.gif',16,16,'','imgmid')+' '+lg('writeArticle')+'</a> | ' : '')
			+ "<a href='#' onclick=\"$p.notebook.addRssWidget('"+__LOCALFOLDER+"notebook/rss_group.php?gid="+vars['groupId']+"','','x');return false;\">"
			+ $p.img('mymodules_add.gif',16,16,'','imgmid')+' '
			+ lg("addNotebookToMyPage")
			+ "</a>"
			+ " | "
			+ "<a href='#' onclick=\"$p.notebook.addRssWidget('"+__LOCALFOLDER+"notebook/comments_rss_group.php?gid="+vars['groupId']+"','comments','x');return false;\">"
			+ $p.img('ico_comment.gif',16,16,'','imgmid')+' '
			+ lg("followCommentsRss")
			+ '</a>'
			+ '</div>'
			+ '<br />';
		
		var l_result=response.getElementsByTagName('article');

		if (vars['search']!=indef)
		{
			l_s += lg('lblResultsFor')+' : <b>'+vars['search']+'</b><br /><br />';
		}

		if (l_result.length==0)
		{
			l_s += (vars['search']==indef ? lg('noArticle') : lg('noResultForThisSearch'));
		}
		else
		{
			for (var i=0;i<$p.min(l_result.length,10);i++)
			{
				var l_id = $p.ajax.getVal(l_result[i],"id","int",false,0),
                    l_status = $p.ajax.getVal(l_result[i],"status","str",false,''),
                    l_owner = $p.ajax.getVal(l_result[i],"ownerid","int",false,0),
                    l_type = $p.ajax.getVal(l_result[i],"type","int",false,0),
                    l_title = $p.ajax.getVal(l_result[i],"title","str",false,'.'),
                    l_desc = $p.ajax.getVal(l_result[i],"description","str",false,''),
                    l_authorName = $p.ajax.getVal(l_result[i],"longname","str",false,'-'),
                    l_authorPicture = $p.ajax.getVal(l_result[i],"picture","str",false,'');

				//specificity for some types
				if (l_type==6) l_title = l_authorName+' '+lg('is')+' : '+l_title;
				if (l_type==4) l_desc = l_desc+'<br /><br /><a href="#" onclick=\'$p.notebook.addPage('+$p.ajax.getVal(l_result[i],"linkedid","int",false,0)+',"");return false;\'>'+$p.img('ico_portal.gif')+' '+lg("useThisPortal")+'</a>';
		
				l_s += '<div id="article'+l_id+'" class="notebookarticle'+l_status+'">'
					+ '<div class="notebookarticletitle"><a href="#" onclick=\'$p.notebook.articles.getDetail('+vars['groupId']+','+l_id+','+vars['page']+',"group")\'>'+l_title+'</a></div>'
					+ '<div class="notebookarticlesummary">'
					+ $p.string.trunk($p.string.removeTags(l_desc),300,50,'$p.notebook.articles.getDetail('+vars['groupId']+','+l_id+','+vars['page']+',"group")')
					+ '</div>'
					+ '<div class="notebookarticleinfo">'
					+ (l_authorPicture=='' ? '' 
                                           : '<div class="picture_image_small">'
                                             + '<img src="'+l_authorPicture+'" />'
                                             + '</div>'
                                             + '<div class="picture_frame_white_small"> </div>'
                                             + '<span style="padding-left: 25px"></span>'
                    )
					+ ' <a href="#" onclick=\'$p.notebook.open('+l_owner+',"group","'+l_authorName+'");return false;\'>'+l_authorName+'</a> | '
					+ $p.date.formatDateLong($p.date.convertFromDb($p.ajax.getVal(l_result[i],"pubdate","str",false,'')),true)+' | '
					+ ($p.ajax.getVal(l_result[i],"tags","str",false,'')=='' ? '' : '<img src="../images/ico_tag.gif" alt="'+lg("tags")+'" align="absmiddle" /> '+$p.ajax.getVal(l_result[i],"tags","str",false,'')+' | ')
					+ $p.img('ico_trackback.gif',16,16,lg('trackback'),'imgmid')+' <a href="#" onclick=\'$p.notebook.articles.getDetail('+vars['groupId']+','+l_id+','+vars['page']+',"group")\'>'+$p.ajax.getVal(l_result[i],"trackbacknb","int",false,0)+' '+lg("trackbacks")+'</a> | '
					+ $p.img('ico_comment.gif',16,16,lg('comments'),'imgmid')+' <a href="#" onclick=\'$p.notebook.articles.getDetail('+vars['groupId']+','+l_id+','+vars['page']+',"group")\'>'+$p.ajax.getVal(l_result[i],"commentnb","int",false,0)+' '+lg("comments")+'</a> | '
					+ (l_owner==$p.app.user.id ? $p.img('ico_add_article.gif',16,16,'','imgmid')+' <a href="#" onclick=\'$p.notebook.articles.modify('+vars['groupId']+','+l_id+','+vars['page']+',"group");\'>'+lg('lblModify')+'</a> | ' : '')
					+ '<a id="article'+l_id+'_optionbtn" href="#" onclick=\'$p.notebook.loadOptions('+l_id+','+l_owner+',"group",'+vars['groupId']+');return false;\'>'+$p.img('ico_down_arrow_black.gif')+' '+lg('options')+'</a>'
					+ '<div class="optiondiv" id="groupbookoptions'+l_id+'">';
                
                var l_artOptions = '';
				if (l_owner == $p.app.user.id)
				{
					l_artOptions += $p.img('ico_notebooksecurity.gif')+' '+lg('defineArticleAccess')
						+ ' <select name="accesslevel" onchange="$p.notebook.articles.setStatus('+l_id+',this.value,'+l_owner+')">'
						+ '<option value="3"'+(l_status=='3' ? ' selected="selected"' : '')+'>'+lg('grouppublicArticle')+'</option>'
						+ '<option value="2"'+(l_status=='2' ? ' selected="selected"' : '')+'>'+lg('groupnetworkArticle')+'</option>'
						+ '<option value="D">'+lg('removeArticle')+'</option>'
						+ '</select>'
						+ '<br />';
				}

				if (l_status > 2)
				{
					l_artOptions += $p.img('ico_get_article.gif')+' '+lg('getInMyNotebook')+' '
						+ ' <select id="groupbook_article'+l_id+'_list"name="accesslevel" onchange="return $p.notebook.trackback('+l_id+','+l_owner+', this.value )">'
						+ '<option value="D" >Loading ...</option>'
						+ '</select>';
				}
                
                if (l_artOptions == '')
                {
                    l_s += lg('nooption');
                }
                else
                {
                    l_s += l_artOptions;
                }
            
				l_s += '</div>'
					+ '</div>'
					+ '</div>';
			}

			//page management
			l_s += '<div class="notebookfooter">'
				+ $p.html.buildPageNavigator('previousPage',(vars['page']==0 ? '' : '$p.groupbook.articles.get('+vars['groupId']+','+(vars['page']-1)+(vars['search']==indef ? '' : ',"'+vars['search']+'"')+')'),'','nextPage',(l_result.length==11 ? '$p.groupbook.articles.get('+vars['groupId']+','+(vars['page']+1)+(vars['search']==indef ? '' : ',"'+vars['search']+'"')+')' : ''))
				+ '</div>';
		}

		$p.print('groupbook'+vars['groupId']+'_content',l_s);
	}
}
$p.groupbook.add={
	isError:0,
	/*
                    Function: $p.groupbook.add.getHtml
                        build form to add new group
                        
                    Returns: 
                        add groupbook form
	 */
	getHtml: function()
	{
		var	l_s = '<div class="title">'+lg('addGroup')+' :</div>'
			+ '<div class="content">'
            + "<form name='frmGroupNotebook' onsubmit='return $p.groupbook.add.insert(this);' >"
            + '<div style="background: url(../images/ico_group_add.gif) top left no-repeat;padding-left: 20px">'
			//+ $p.img('ico_group_add.gif',16,16,'','imgmid')+'&nbsp;'
			+ '<input type="text" name="txtNewNotebookGroup" class="thinbox" maxlength="100" style="color: #aaaaaa;width: 200px;" onFocus=\'$p.app.tools.inputFocus(this,"'+lg('Name')+'")\' onBlur=\'$p.app.tools.inputLostFocus(this,"'+lg('Name')+'");\' value="'+lg('Name')+'" />'
            + '<br />'+lg('lblDescription')
            + '<br /><textarea name="descNewNotebookGroup" rows="2" style="width: 200px"></textarea>'
			+ '<br/><br/>'
			+ '<input type="checkbox" name="chkPrivate" value="1" >' + lg('IamTheOnlyOneToAddMembers')+'<br/><br/>'
			+ '<input style="margin-left:100px;" type="submit" class="submit" name="btnAdd" value="' + lg('add')+'" onclick="$p.groupbook.add.isExists();">'
            + '</div>'
			+ '</form>'
			+ '</div>'
			+ '<div class="foooter">&nbsp;</div>';

		return l_s;		
	},

	/*
	 * $p.groupbook.add.isExists : check if captured name notebook group is already exist or not
	 */
	isExists:function() {
		var v_newNotebook = document.forms["frmGroupNotebook"].txtNewNotebookGroup.value;
		 
		if ("" != _trim(v_newNotebook)) {
			$p.ajax.call(pep["xmlgroupbook_exists"]+'?name='+v_newNotebook,
				{
					'type':'load',
					'callback':
					{
						'function':$p.groupbook.add.initExist
					}
				}
			);
		}
		else
		{
			$p.groupbook.add.isError = 1;
			$p.app.alert.show(lg("mandatoryNotebookName"));
		}
		return false;
	},

	initExist:function(response,vars)
	{
		$p.groupbook.add.isError = $p.ajax.getVal(response, "exist", "int", true, 0);
		if($p.groupbook.add.isError!=0)
			$p.app.alert.show(lg("alreadyExists"));		
	},

	/*
	 * $p.groupbook.add.insert : insert new notebook group in database
	 */
	insert:function(v_form)
	{
        var v_newNotebook = v_form.txtNewNotebookGroup.value;

        if (v_newNotebook == '' || v_newNotebook == lg('Name')) return false;

		$p.group.reset();

		if ($p.groupbook.add.isError == 0)
        {
			var v_chkPrivate = (true==v_form.chkPrivate.checked)?1:0;

			$p.ajax.call(pep["scr_groupbook_add"],
				{
					'type':'execute',
					'variables':'act=add&pv='+v_chkPrivate+'&name='+$p.string.esc(v_newNotebook)+'&desc='+$p.string.esc(v_form.descNewNotebookGroup.value),
					'alarm':false
				}
			);
			$p.network.dashboard.getUserWorkingGroups();
		}
		return false;
	},
	/*
	 * $p.groupbook.add.join : user join to notebook group
	 *  input : notebook group id
	 */
	join:function(v_groupId,v_groupName)
	{
		//reset the user group
		$p.group.reset();
		$p.ajax.call(pep["scr_groupbook_add"],
			{
				'type':'execute',
				'variables':'act=join&id='+v_groupId+'&name='+v_groupName,
				'alarm':false,
				'forceExecution':false,
				'callback':
				{
					'function':$p.network.dashboard.getUserWorkingGroups
				}
			}
		);
	}
}
$p.groupbook.profile={
	desc:"",
	/*
		$p.groupbook.profile.getDesc : display my profile formated description 
		inputs : define if the description is truncated or not
	*/
	getDesc:function(v_trunc)
	{
		var l_desc=(v_trunc && $p.groupbook.profile.desc.length>150)?$p.groupbook.profile.desc.substr(0,150)+" ...[<a href='#' onclick='$p.groupbook.profile.getDesc(false);return false;'>"+lg("readMore")+"</a>]":$p.groupbook.profile.desc;
		$p.print("groupbookdesc",l_desc);
	}
}
$p.groupbook.comment={
	/*
		$p.groupbook.comment.remove : remove comment
		inputs
			v_id: comment id
	*/
	remove:function(v_id)
	{
		var response=confirm(lg("readyToSuppressComment"));
		if (response!=1) return false;
		$p.ajax.call(pep["scr_groupbook_removecomment"],
			{
				'type':'execute',
				'variables':'id='+v_id,
				'alarm':true,
				'forceExecution':true,
				'callback':
				{
					'function':$p.groupbook.comment.confirmRemove
				}
			}
		);
		return false;
	},
	/*
		$p.groupbook.comment.confirmRemove : confirm comment suppression
		input : xml file return
	*/
	confirmRemove:function(v_ret)
	{
		if (v_ret!=indef)
		{
			$p.app.alert.show(lg("modificationApplied"));
			navShow("comment"+v_ret, "none");
		}
	}
}
$p.groupbook.sidebar={
	/*
		$p.groupbook.sidebar.getArticleTags : get tags list of an article
		input : article id, groupe id
	*/
	getArticleTags:function(v_artId, v_groupId) {
		navWait("tagslistdiv");
		$p.ajax.call(pep["xmlgroupbook_tagsarticle"]+'?artId='+v_artId+'&gid='+v_groupId,
			{
				'type':'load',
				'callback':
				{
					'function':$p.groupbook.sidebar.displayArticleTags,
					'variables':
					{
						'groupid':v_groupId
					}
				}
			}
		);
	},
	
	/*
		$p.groupbook.sidebar.displayArticleTags : refresh tags list of an article
	*/
	displayArticleTags:function(response, vars) {
		var result=response.getElementsByTagName("tag");
		l_s="";
		
		if ( result.length > 0)
		{
			for (var i=0; i<result.length; i++)
			{
				var l_id=$p.ajax.getVal(result[i], "id", "int",false,0);
				var l_label=$p.ajax.getVal(result[i],"label","str",false,"");
				var l_nb=$p.ajax.getVal(result[i], "nb", "int",false,0);
				l_s+= "<a href='"+pep["index"]+"?id="+vars['groupid']+"&search="+l_label+"&searchid="+l_id+"&type=tag'>"+l_label+"("+l_nb+")</a> &nbsp; ";
			}
		}
		$p.print("tagslistdiv", l_s);
	}
}
$p.groupbook.article={
	/*
		$p.groupbook.article.setStatus : set access type to groupbook articles
		inputs
			v_id: article id
			v_status : new status
			v_groupId : notebookgroup id
	*/
	setStatus:function(v_id,v_status,v_groupId)
	{
		if (v_status=="D")
		{
			var response=confirm(lg("msgArchSup"));
			if (response!=1) return false;
		}
		$p.ajax.call(pep["scr_groupbook_changearticlestatus"],
			{
				'type':'execute',
				'variables':'id='+v_id+'&status='+v_status+'&group='+v_groupId,
				'alarm':true,
				'forceExecution':true,
				'callback':
				{
					'function':$p.groupbook.article.setStatusApplied
				}
			}
		);
	},
	/*
		$p.groupbook.article.setStatusApplied :
		input : xml file return
	*/
	setStatusApplied:function(v_ret)
	{
		$p.app.alert.show(lg("modificationApplied"));
		var l_ret=v_ret.split("_");
		navClass("article"+l_ret[0],"notebookarticle"+l_ret[1]);
		if ("D"==l_ret[1]) {
			$p.groupbook.sidebar.getArticleTags(l_ret[0], l_ret[2]);
		}
	}
};

$p.notebook={
	opened:[],
	inFrame:false,
/*	initMenu:function()
	{
		//if ($p.app.user.id>0) $p.app.menu.options.push({"id":"notebook","label":lg("Notebook"),"desc":lg("notebookIconDesc"),"icon":"ico_notebook.gif","seq":30,"action":"","type":"","subOpt":[{"id":"pmenunotebooktoread","label":"myNotebook","seq":0,"action":"$p.notebook.open()","pages":[]},{"id":"pmenunotebooktoread","label":"lblArchive","seq":1,"action":"$p.notebook.menu()","pages":[]}],"pages":[]});
		if ($p.app.user.id>0) $p.app.menu.options.push({"id":"notebook","label":lg("Notebook"),"desc":lg("notebookIconDesc"),"icon":"ico_notebook.gif","seq":30,"action":"$p.notebook.menu()","type":"","subOpt":[],"pages":[]});
	},
*/
	menu: function()
	{
        $p.app.menu.addTitle('notebookinfomenu_1','ico_notebook.gif',lg("myNotebook"));
		var l_s = ''
			+ '<br />'
			+ $p.img('ico_notebook.gif',16,16,'','imgmid')
			+ '&nbsp;<a href="#" onclick="$p.notebook.open();return false;">'+lg('myNotebook')+'</a>'
			+ '<br /><br />'
			+ '<div class="title">'+lg('lblArchive')+'</div>'
			+ $p.img('puce.gif')
			+ '&nbsp;<a href="#" onclick=\'$p.notebook.getRecentComments();return false;\'>'+lg('lastComments')+'</a><br />'
			+ $p.img('puce.gif')
			+ '&nbsp;<a href="#" onclick=\'$p.notebook.getmostcommented();return false;\'>'+lg('MostCommented')+'</a>';	
            
        $p.app.menu.addArea('notebookinfomenu_1',l_s);

		$p.app.menu.addTitle('notebookinfomenu_2','',lg('notebooksOfMyNetwork'),$p.notebook.getNetworkNotebooks);
		$p.app.menu.addArea('notebookinfomenu_2','<div id="networknotebooksmenu"></div>');

		$p.app.menu.addTitle('notebookinfomenu_3','',lg('notebooksOfMyGroups'),$p.notebook.getGroupsNotebooks);
		$p.app.menu.addArea('notebookinfomenu_3','<div id="groupsnotebooksmenu"></div>');

		$p.app.menu.openSubMenu('notebookinfomenu_1',true);
	},
	/*
		getNetworkNotebooks : load notebooks of user's network
	*/
	getNetworkNotebooks: function()
	{
		$p.ajax.call(pep["xmlnetwork_users"]+'?kwid=0&s=0',
			{
				'type':'load',
				'callback':
				{
					'function':$p.notebook.displayNetworkNotebooks
				}
			}
		);
	},
	displayNetworkNotebooks: function(response,vars)
	{
		var l_s = '',l_result = response.getElementsByTagName('user');

		for (var i = 0;i < l_result.length;i++)
		{
			var l_name = $p.ajax.getVal(l_result[i],'longname','str',false,'...');
			var l_picture = $p.ajax.getVal(l_result[i],'picture','str',false,'')
			if (l_picture == '') l_picture = '../images/nopicture.gif';
	
			l_s += '<div class="picture_image_small">'
                + '<img src="'+l_picture+'" />'
                + '</div>'
                + '<div class="picture_frame_white_small"> </div>'
                + '<span style="padding-left: 25px"></span>'
				+ '&nbsp;'
				+ '<a href="#" onclick=\'$p.notebook.open('+$p.ajax.getVal(l_result[i],'id','int',false,0)+',"note","'+l_name+'",indef,indef,"'+l_picture+'")\'>'+l_name+'</a><br />';
		}

		$p.print('networknotebooksmenu',l_s);
	},
	/*
		getGroupsNotebooks : load notebooks of user's groups
	*/
	getGroupsNotebooks: function()
	{
		$p.ajax.call(pep["xmlnetwork_userworkinggroups"],
			{
				'type':'load',
				'callback':
				{
					'function':$p.notebook.displayGroupsNotebooks
				}
			}
		);
	},
	displayGroupsNotebooks: function(response,vars)
	{
		var l_s = '';
		var l_result = response.getElementsByTagName('workinggroup');

		for (var i = 0;i < l_result.length;i++)
		{
			var l_name = $p.ajax.getVal(l_result[i],'name','str',false,'...');
			l_s += $p.img('puce.gif')+'&nbsp;'
				+ '<a href="#" onclick=\'$p.notebook.open('+$p.ajax.getVal(l_result[i],'id','int',false,0)+',"group","'+l_name+'")\'>'+l_name+'</a><br />';
		}

		$p.print('groupsnotebooksmenu',l_s);
	},
	summary: function(v_div)
	{
		var l_s = $p.html.buildTitle(lg('myNotebook')+' : '+lg('lastComments'))
			+ '<div id="notebooksummary"></div>';
		navPrint(v_div,l_s);

		$p.notebook.getContributions(0,'notebooksummary');
	},
	getRecentComments: function()
	{
		$p.network.init();

		$p.print('network_content',$p.html.buildTitle(lg('Notebook')+' : '+lg('lastComments'))+'<div id="mostcommentedarticles"></div>');

		$p.notebook.getContributions(0,'mostcommentedarticles');
	},
	/*
		$p.notebook.addArticle : add an article to notebook
		inputs :
			v_title : article title
			v_desc : article description
			v_keywords : article keywords list (coma separated)
			v_type : article type (2=standard, 3=module 4=portal)
			v_articleid : article id (from feed_articles table, if applicable)
			v_linkedObjId : id of linked object (widget id, portal id, article id, ..., if applicable)
			v_access : access code (3=public, 2=my network 1=private)
			v_objectId : 
			v_tGroup : array of groups in which to share article
	*/
	addArticle: function(v_title,v_desc,v_keywords,v_type,v_articleId,v_linkedObjId,v_access,v_objectId, v_tGroup)
	{
		v_keywords = $p.tags.formatList(v_keywords);
		var l_kwformated = $p.string.formatForSearch(v_keywords);
		var l_vars = [];
		if (undefined != v_tGroup) {
			var j = 0;
			for(i = 0;i < v_tGroup.length; i++)
			{
				l_vars.push("gId"+j+"="+v_tGroup[i]);
				j++;
			}
		}

		//save article and call $p.network.addNews with xml response
		$p.ajax.call(pep["scr_notebook_articleadd"],
			{
				'type':'execute',
				'variables':'pubtitle='+$p.string.esc(v_title)+'&desc='+$p.string.esc(v_desc)+'&faid='+v_articleId+'&type='+v_type+'&linked='+v_linkedObjId+'&access='+v_access+'&kw='+v_keywords+'&kwformated='+l_kwformated+'&oid='+v_objectId+'&'+l_vars.join('&'),
				'alarm':true,
				'forceExecution':false,
				'callback':
				{
					'function':$p.network.addNews,
					'variables':
					{
						'access':v_access
					}
				}
			}
		);
	},
	/*
		$p.notebook.open : open my notebook
		inputs
			v_id : id of the notebook
			v_type : type of the notebook (personal / group)
			v_name : name of the notebook
			v_articleid : article ID
			v_picture : picture of the notebook
			v_anchor : position on the anchor once opened
	*/
	open:function(v_id,v_type,v_name,v_option,v_articleid,v_picture,v_anchor)
	{
		if (v_type==indef)
			v_type='note';
		if (v_type=='note')
		{
			if (v_id==indef)
				v_id=$p.app.user.id;
			if (v_id==$p.app.user.id)
				v_name=lg('myNotebook');
		}
		$p.notebook.init(v_name);

		$p.notebook.display(v_id,v_type,v_name,v_option,v_articleid,v_picture,v_anchor);
	},
	/*
		$p.notebook.init : init the notebook interface
	*/
	init:function()
	{
		$p.app.newEnv('notebook');
	/*
		$p.plugin.open();
		$p.plugin.init(lg("notebook"),'notebook');
		$p.plugin.useWidget();

		var l_height=Window.getHeight()-getPos($('plugincontent'),"Top")-40+(getPos($('pluginmenu'),"Top")-getPos($('menus'),"Top"));

		$p.plugin.content('<iframe id="notebookframe" name="notebookframe" src="" width="100%" height="'+l_height+'" frameborder=0></iframe>');
	*/
	},
	/*
		$p.notebook.display : display a notebook
		input : id of the notebook
	*/
	display:function(v_id,v_type,v_name,v_option,v_articleid,v_picture,anchor)
	{
		$p.app.tabs.openTempTab(3,"$p.plugin.openInTab(%tabid%,function(){},'notebook/"+v_type+"/"+v_id+"')",v_name,'../images/ico_notebook.gif');

		switch(v_type)
		{
			case 'note':
				$p.notebook.build(v_id,v_name,v_picture,v_option,v_articleid,anchor);
				break
			case 'group':
				$p.groupbook.build(v_id,v_name,v_picture,v_option,v_articleid,anchor);
				break
		}

		$p.app.setState('$p.notebook.open('+v_id+',"'+v_type+'","'+v_name+'",'+v_option+','+v_articleid+',"'+v_picture+'")');

		//$p.app.tabs.openTempLink(v_name,__LOCALFOLDER+l_link);
	},
	/*
		$p.notebook.build : build a personal notebook
		Parameters:

			v_userId : user ID
			v_title : notebook title
			v_option : tab to open in the notebook
			v_picture : notebook picture
			v_articleid : notebook article
			v_anchor : HTML anchor to go to, once opened
	*/
	build: function(v_userId,v_title,v_picture,v_option,v_articleid,v_anchor)
	{
		var l_s = '<div class="feature">'
            + '<div id="notebook'+v_userId+'_header"></div>'
			+ '<div id="notebook'+v_userId+'_content" class="content"></div>'
			+ '</div>';
		$p.print('modules'+tab[$p.app.tabs.sel].id,l_s);

        $p.notebook.loadProperties(v_userId);

		$p.notebook.selectMenuOption(v_userId,v_option,{'articleid':v_articleid,'anchor':v_anchor});
	},
	/*
		$p.notebook.loadProperties : build a personal notebook header
		Parameters:

			v_userId : user ID
	*/
    loadProperties: function(v_userId)
    {
        $p.ajax.call(pep["xml_notebook_properties"]+'?id='+v_userId,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.notebook.displayProperties,
                    'variables':
                    {
                        'userId':v_userId
                    }
                },
                'asynchron':false
            }
        );
    },
    displayProperties: function(response,vars)
    {
        var l_result = response.getElementsByTagName('notebook');
        var isFriend = $p.ajax.getVal(l_result[0],'isfriend','str','');
        var l_picture = $p.ajax.getVal(l_result[0],'picture','str','');

        var l_s = $p.html.buildFeatureHeader({
            'image':(l_picture == '' ? '../images/nopicture.gif' : l_picture),
            'title':((isFriend == '' && vars['userId'] != $p.app.user.id) ? '<div style="float: right"><span style="font-size: 11px"><a href="#" onclick="$p.network.card.load('+vars['userId']+');return false;">'+$p.img('ico_friend_add.gif',16,16,'','imgmid')+' '+lg('addToMyNetwork')+'</a></span></div>' : '')
                    +(vars['userId'] == $p.app.user.id ? lg('myNotebook')
                                                       : $p.ajax.getVal(l_result[0],'longname','str','')                                                            
                    ),
            'menu':'<div id="notebook'+vars['userId']+'_menu"></div>'
        });
        $p.print('notebook'+vars['userId']+'_header',l_s);
    },
	/*
		$p.notebook.buildMenu : build a personal notebook menu
		Parameters:

			v_userId : user ID
	*/
	buildMenu:function(v_userId,v_selOption)
	{
		var l_h=[];
		if (v_userId != $p.app.user.id)
            l_h.push({'id':1,'fct':'$p.notebook.selectMenuOption('+v_userId+',1);','icon':'ico_myaccount.gif','label':lg('profil')});
		l_h.push({'id':2,'fct':'$p.notebook.selectMenuOption('+v_userId+',2);','icon':'ico_notebook.gif','label':lg('publications')});
		if (__networkIsPublic)
		{
			l_h.push({'id':3,'fct':'$p.notebook.selectMenuOption('+v_userId+',3);','icon':'mynetwork.gif','label':lg('network')});
		}
        if (v_userId != $p.app.user.id)
            l_h.push({'id':7,'fct':'$p.notebook.selectMenuOption('+v_userId+',7);','icon':'','label':lg('Groups')});
		l_h.push({'id':4,'fct':'$p.notebook.selectMenuOption('+v_userId+',4);','icon':'ico_comment.gif','label':lg('contributions')});
		l_h.push({'id':5,'fct':'$p.notebook.selectMenuOption('+v_userId+',5);','icon':'mymodules_add.gif','label':lg('lblModules')});
		if (v_userId==$p.app.user.id)
		{
			l_h.push({'id':6,'fct':'$p.notebook.selectMenuOption('+v_userId+',6);','icon':'ico_write.gif','label':lg('writeArticle')});
		}

		$p.print('notebook'+v_userId+'_menu',$p.html.buildFeatureMenu(v_selOption,l_h));
	},
	/*
		$p.notebook.selectMenuOption : select a menu option
		Parameters:

			v_userId : user ID
			v_selOption : option selected
	*/
	selectMenuOption: function(v_userId,v_selOption,v_extra)
	{
		if (v_selOption==indef) v_selOption=2;

		$p.notebook.buildMenu(v_userId,v_selOption);

		switch(v_selOption)
		{
			case 1:
				$p.notebook.profile.get(v_userId);
				break;
			case 2:
				if (v_extra==indef || v_extra['articleid']==indef)
				{
					$p.notebook.articles.get(v_userId);
				}
				else
				{
					$p.notebook.articles.getDetail(v_userId,(v_extra==indef ? indef : v_extra['articleid']),0,'note',(v_extra==indef ? indef : v_extra['anchor']));
				}
				break;
			case 3:
				$p.notebook.network.get(v_userId,0);
				break;
			case 4:
				$p.notebook.contributions.get(v_userId);
				break;
			case 5:
				$p.notebook.getUserWidgets(v_userId);
				break;
			case 6:
				$p.notebook.articles.write(v_userId,'note');
				break;
            case 7:
				$p.group.getUserGroups(v_userId);
				break;
		}
	},
	/*
		$p.notebook.checkInclusion : check if the notebook is in a frame or not
	*/
	checkInclusion:function()
	{
		if (parent.window.location!=window.location)
		{
			$p.notebook.inFrame=true;
			navShow('header','none');
			$('notebook').style.width='100%';
		}
	},
	/*
		$p.notebook.openLinkInNotebook : open a Notebook link depending if the notebook is inside Portaneo or in another page
		inputs
			v_type : link type
			v_id : id of the object to open
			v_name : name of the notebook that will be opened
	*/
	openLinkInNotebook:function(v_type,v_id,v_name,v_articleid)
	{
/*
		if (v_name==indef) v_name=$p.app.user.name;
		if ($p.notebook.inFrame)
		{
			parent.$p.notebook.open(v_id,v_type,v_name,v_articleid);
			$p.app.setState('$p.notebook.open('+v_id+',"'+(v_type=='detail'?'index':v_type)+'","'+v_name+'")');
		}
		else
		{
			switch(v_type)
			{
				case 'index':
					var l_link=pep["notebook_index2"]+'?id='+v_id;
					break;
				case 'detail':
					var l_link=pep["detail"]+'?artid='+v_id;
					break;
			}
			$p.url.openLink(l_link,true);
		}
*/
	},
	/*
		$p.notebook.addRssWidget : add notebook information in a widget
		Input :
			v_rss: rss feed
			v_name : name of the widget
	*/
	addRssWidget:function(v_rss,v_name,v_auth)
	{
		var l_rss=$p.string.unesc(v_rss);
		$p.app.widgets.rss.checkFeed(l_rss,$p.string.unesc(v_name),v_auth);
/*		if ($p.notebook.inFrame)
		{	
			var l_rss=$p.string.unesc(v_rss);
			parent.$p.app.widgets.rss.checkFeed(l_rss,$p.string.unesc(v_name));
			//$p.notebook.addWidget(86,'rssurl='+v_rss+'&ptitl='+v_name);
		}
		else
		{
			$p.url.openLink(pep["addtoapplication2"]+'?name='+v_name+"&url="+v_rss);
		}
*/
	},
	addWidget:function(v_id,v_vars)
	{
		parent.$p.app.widgets.open(v_id,v_vars,'uniq');
	},
	/*
		$p.notebook.addPage : add a page from notebook
		Inputs
			v_pageid : page ID
			v_check : security MD5 string
	*/
	addPage:function(v_pageid,v_check)
	{
		if ($p.notebook.inFrame)
		{
			parent.$p.app.pages.loadSharedPortal(v_pageid,2,v_check,false);
		}
		else
		{
			$p.url.openLink(pep["addportaltoapplication2"]+'?id='+v_pageid+'&chk='+v_check);
		}
	},
	/*
		$p.notebook.getmostcommented : get most commented notebook articles
	*/
	getmostcommented:function(v_page)
	{
		if (v_page==indef) v_page=0;
		$p.network.information.open();
		if ($p.app.user.id==0)	$('network_content').set('html',lg('msgNeedToBeConnectedPage'));
		else
		{
			navWait('network_content');
			$p.ajax.call(pep["xmlmostcomments"]+'?p='+v_page,
				{
					'type':'load',
					'callback':
					{
						'function':$p.notebook.displayMostCommented,
						'variables':
						{
							'page':v_page
						}
					}
				}
			);
		}
	},
	/*
		$p.notebook.displayContributions: display notebook summary
	*/
	displayMostCommented:function(response,vars)
	{
		var l_s=$p.html.buildTitle(lg('Notebook')+' : '+lg('MostCommented'));
		var l_result=response.getElementsByTagName("item");

		if(l_result.length==0){
			l_s+="<b>"+lg("noCommentary");+"</b>"
		}
		else
		{
			l_s+='<ul>';
			for (var i=0;i<l_result.length;i++)
			{
				l_s+='<li>'
					+'<a href="#" onclick=\'$p.notebook.open('+$p.app.user.id+',"note","'+$p.app.user.name+'",indef,'+$p.ajax.getVal(l_result[i],"id","int",false,0)+',indef,"comments")\'>'
					+$p.ajax.getVal(l_result[i],"title","str",false,'???')
					+'</a>'
					+' ('
					+$p.ajax.getVal(l_result[i],"commentsnb","int",false,0)
					+' '+lg('comments')
					+')'
					+'</li>';
			}

			l_s+='</ul>'
				+$p.html.buildPageNavigator('previous',(vars['page']==0 ? '' : '$p.notebook.getmostcommented('+(vars['page']-1)+');return false;'),'','next',(l_result.length!=20 ? '' : '$p.notebook.getmostcommented('+(vars['page']+1)+');return false;'));
		}
		$p.print('network_content',l_s);
	},
	/*
		$p.notebook.getContributions : get notebook contributions
	*/
	getContributions:function(v_page,v_div)
	{
		if (v_page==indef) v_page=0;
		if ($p.app.user.id==0)	$(v_div).set('html',lg('msgNeedToBeConnectedPage'));
		else
		{
			navWait(v_div);
			$p.ajax.call(pep["xmlmycomments"]+'?p='+v_page,
				{
					'type':'load',
					'callback':
					{
						'function':$p.notebook.displayContributions,
						'variables':
						{
							'page':v_page,
							'divid':v_div
						}
					}
				}
			);
		}
	},
	/*
		$p.notebook.displayContributions: display notebook summary
	*/
	displayContributions:function(response,vars)
	{
		var l_s='';
		var l_result=response.getElementsByTagName("item");

		if(l_result.length==0){
			l_s+="<b>"+lg("noCommentary");+"</b>"
		}
		else
		{
			l_s+='<ul>';
			for (var i=0;i<l_result.length;i++)
			{
				var l_name=$p.ajax.getVal(l_result[i],"user","str",false,"???");
				l_s+='<li>'
					+'<a href="#" onclick=\'$p.notebook.open('+$p.ajax.getVal(l_result[i],"userid","int",false,0)+',"note","'+l_name+'")\'>'+l_name+'</a>'
					+' : '
					+'<a href="#" onclick=\'$p.notebook.open('+$p.app.user.id+',"note","'+$p.app.user.name+'",indef,'+$p.ajax.getVal(l_result[i],"articleid","int",false,0)+',indef,"comments")\'>'
					+$p.string.trunk($p.ajax.getVal(l_result[i],"message","str",false,"???"),60)
					+'</a>'
					+'</li>';
			}

			l_s+='</ul>'
				+$p.html.buildPageNavigator('previous',(vars['page']==0 ? '' : '$p.notebook.getContributions('+(vars['page']-1)+',"'+vars['divid']+'");return false;'),'','next',(l_result.length!=20 ? '' : '$p.notebook.getContributions('+(vars['page']+1)+',"'+vars['divid']+'");return false;'));
		}
		$p.print(vars['divid'],l_s);
	},
	loadOptions: function(v_articleId,v_owner,v_type,v_groupId)
	{
		if (v_groupId == indef) v_groupId = 0;

		if ($p.isShown(v_type+'bookoptions'+v_articleId))
		{
			$p.show(v_type+'bookoptions'+v_articleId,'none');
			$p.setClass('article'+v_articleId+'_optionbtn','');
		}
		else
		{
			$p.show(v_type+'bookoptions'+v_articleId,'block');
			if ($(v_type+'book_article'+v_articleId+'_list')!=null)
				$p.group.get("$p.notebook.showOptions("+v_articleId+","+v_owner+",'"+v_type+"',"+v_groupId+")");
			$p.setClass('article'+v_articleId+'_optionbtn','linkopeningoptiondiv_selected');
		}
	},
	showOptions: function(v_articleId,v_owner,v_type,v_groupId)
	{
		$(v_type+'book_article'+v_articleId+'_list').options[0]=new Option('== '+lg('selectBook')+' ==','D');
		
		var optNb = 1;
		if (v_owner != $p.app.user.id)
		{
			$(v_type+'book_article'+v_articleId+'_list').options[1]=new Option(lg("myNotebook"),'id_'+v_owner);
			optNb++;
		}
		for (var i = 0;i < $p.group.list.length;i++)
		{
			if (v_type == 'note' || v_groupId != $p.group.list[i].id)
			{
				$(v_type+'book_article'+v_articleId+'_list').options[optNb]=new Option($p.group.list[i].label,'gid_'+$p.group.list[i].id);
				optNb++;
			}
		}
	},
	/*
		$p.notebook.trackback : get an article in my notebooks
		inputs : 
		* v_articleId : article id 
		* v_ownerId  : article's owner 
		* v_value : accesslevel action to do 
	*/
	trackback:function(v_articleid, v_ownerid, v_value)
	{
		$p.ajax.call(pep["scr_notebook_trackback"],
			{
				'type':'execute',
				'variables':'artid='+v_articleid+'&owner='+v_ownerid+'&source='+v_value,
				'alarm':false,
				'forceExecution':false,
				'callback':
				{
					'function':$p.notebook.trackbackSuccess
				}
			}
		);
		
		return false;
	},	
	
	/*
		$p.notebook.trackbackSuccess : message when the article is saved in a notebook
	*/
	trackbackSuccess: function()
	{
		$p.app.alert.show(lg("trackbackSuccess"));
	},
	/*
		$p.notebook.getUserWidgets : Get public widgets of a user
		Parameters:

			v_userId : user ID
	*/
	getUserWidgets: function(v_userId)
	{
		var l_s = $p.html.buildTitle(lg('publicWidgets'))
			+ '<div id="notebook'+v_userId+'_widgetlist">';
		$p.print('notebook'+v_userId+'_content',l_s);

		$p.app.pages.getPublicWidgets(v_userId,'notebook'+v_userId+'_widgetlist');
	},
    addPortalAddingLink: function(v_desc,v_id)
    {
        return v_desc
            + '<br /><br />'
            + '<a href="#" onclick=\'$p.app.pages.loadSharedPortal('+ v_id+',1,indef,true);return false;\'>'
            + $p.img('ico_portal.gif')
            + ' '+lg("useThisPortal")
            + '</a>';
    },
    addWidgetAddingLink: function(v_desc)
    {
        return v_desc.replace('act=','onclick=');
    }
}
$p.notebook.articles={
	/*
		$p.notebook.articles.get : Get notebook articles
		Parameters:

			v_userId : user ID
			v_page : page number
			v_search : search text (string)
	*/
	get:function(v_userId,v_page,v_search)
	{
		if (v_page==indef) v_page=0;

		$p.app.wait('notebook'+v_userId+'_content');

		$p.ajax.call(pep["notebook_xmlarticles"]+'?id='+v_userId+'&page='+v_page+(v_search==indef ? '' : '&search='+v_search+'&type=plaintxt'),
			{
				'type':'load',
				'callback':
				{
					'function':$p.notebook.articles.display,
					'variables':
					{
						'userId':v_userId,
						'page':v_page,
						'search':v_search
					}
				}
			}
		);
	},
	/*
		$p.notebook.articles.display : Display notebook articles
		Parameters:

			response : XML response
			vars : variables sent
	*/
	display:function(response,vars)
	{
		var l_options = '<form onsubmit="$p.notebook.articles.get('+vars['userId']+',0,this.search.value);return false;" style="padding: 0px;margin: 0px;">'
			+ '<input class="thinbox" type="text" name="search" value="'+(vars['search']==indef ? lg('Search') : vars['search'])+'" /> <input class="thinbox" type="submit" value="'+lg('ok')+'" />'
			+ '</form>';

		var l_s = $p.html.buildTitle(lg('publications'),l_options)
			+ '<div style="width: 100%;text-align:right;">'
            + '<a href="#" onclick=\'$p.notebook.articles.get('+vars['userId']+','+(vars['page'])+(vars['search']==indef ? '' : ',"'+vars['search']+'"')+');return false;\'>'+$p.img('ico_refresh2.gif',16,16,'','imgmid')+' '+lg('lblRefresh')+'</a> | '
			+ (vars['userId'] == $p.app.user.id ? '<a href="#" onclick=\'$p.notebook.articles.write('+vars['userId']+',"note");\'>'+$p.img('ico_write.gif',16,16,'','imgmid')+' '+lg('writeArticle')+'</a> | ' : '')
			+ "<a href='#' onclick=\"$p.notebook.addRssWidget('"+__LOCALFOLDER+"notebook/rss.php?id="+vars['userId']+"','','x');return false;\">"
			+ $p.img('mymodules_add.gif',16,16,'','imgmid')+' '
			+ lg("addNotebookToMyPage")
			+ "</a>"
			+ " | "
			+ "<a href='#' onclick=\"$p.notebook.addRssWidget('"+__LOCALFOLDER+"notebook/comments_rss.php?id="+vars['userId']+"','comments','x');return false;\">"
			+ $p.img('ico_comment.gif',16,16,'','imgmid')+' '
			+ lg("followCommentsRss")
			+ '</a>'
			+ '</div>'
			+ '<br />';

		var l_result = response.getElementsByTagName('article');

		if (vars['search'] != indef)
		{
			l_s += lg('lblResultsFor')
                + ' : <b>'+vars['search']+'</b>'
                + '<br /><br />';
		}

		if (l_result.length==0)
		{
			l_s += (vars['search'] == indef ? lg('noArticle') : lg('noResultForThisSearch'));
		}
		else
		{
			for (var i = 0;i < $p.min(l_result.length,10);i++)
			{
				var l_id = $p.ajax.getVal(l_result[i],"id","int",false,0);
				var l_status = $p.ajax.getVal(l_result[i],"status","str",false,'');
				var l_owner = $p.ajax.getVal(l_result[i],"ownerid","int",false,0);
				var l_type = $p.ajax.getVal(l_result[i],"type","int",false,0);
				var l_title = $p.ajax.getVal(l_result[i],"title","str",false,'.');
				var l_desc = $p.ajax.getVal(l_result[i],"description","str",false,'');
				var l_authorName = $p.ajax.getVal(l_result[i],"longname","str",false,'-');
				var l_authorPicture = $p.ajax.getVal(l_result[i],"picture","str",false,'');
		
				//specificity for some types
				if (l_type == 6) l_title = l_authorName+' '+lg('is')+' : '+l_title;
                if (l_type == 4) l_desc = $p.notebook.addPortalAddingLink(l_desc,$p.ajax.getVal(l_result[i],"linkedid","int",false,0));
                if (l_type == 3) l_desc = $p.notebook.addWidgetAddingLink(l_desc);
		
				l_s += '<div id="article'+l_id+'" class="notebookarticle'+l_status+'">'
					+ '<div class="notebookarticletitle"><a href="#" onclick=\'$p.notebook.articles.getDetail('+vars['userId']+','+l_id+','+vars['page']+')\'>'+l_title+'</a></div>'
					+ '<div class="notebookarticlesummary">'
					+ $p.string.trunk($p.string.removeTags(l_desc),300,50,'$p.notebook.articles.getDetail('+vars['userId']+','+l_id+','+vars['page']+')')
					+ '</div>'
					+ '<div class="notebookarticleinfo">'
					+ (l_authorPicture == '' ? ''
                                             : '<div class="picture_image_small">'
                                               + '<img src="'+l_authorPicture+'" />'
                                               + '</div>'
                                               + '<div class="picture_frame_white_small"> </div>'
                                               + '<span style="padding-left: 25px"></span>'
                    )
					+ ' <a href="#" onclick=\'$p.notebook.open('+l_owner+',"note","'+l_authorName+'",indef,indef,"'+l_authorPicture+'");return false;\'>'+l_authorName+'</a> | '
					+ $p.date.formatDateLong($p.date.convertFromDb($p.ajax.getVal(l_result[i],"pubdate","str",false,'')),true)+' | '
					+ ($p.ajax.getVal(l_result[i],"tags","str",false,'')=='' ? '' : '<img src="../images/ico_tag.gif" alt="'+lg("tags")+'" align="absmiddle" /> '+$p.ajax.getVal(l_result[i],"tags","str",false,'')+' | ')
					+ $p.img('ico_trackback.gif',16,16,lg('trackback'),'imgmid')+' <a href="#" onclick="$p.notebook.articles.getDetail('+vars['userId']+','+l_id+','+vars['page']+')">'+$p.ajax.getVal(l_result[i],"trackbacknb","int",false,0)+' '+lg("trackbacks")+'</a> | '
					+ $p.img('ico_comment.gif',16,16,lg('comments'),'imgmid')+' <a href="#" onclick="$p.notebook.articles.getDetail('+vars['userId']+','+l_id+','+vars['page']+')">'+$p.ajax.getVal(l_result[i],"commentnb","int",false,0)+' '+lg("comments")+'</a> | '
					+ (l_owner == $p.app.user.id ? $p.img('ico_add_article.gif',16,16,'','imgmid')+' <a href="#" onclick=\'$p.notebook.articles.modify('+vars['userId']+','+l_id+','+vars['page']+',"note");\'>'+lg('lblModify')+'</a> | ' : '')
					+ '<a id="article'+l_id+'_optionbtn" href="#" onclick=\'$p.notebook.loadOptions('+l_id+','+l_owner+',"note");return false;\'>'+$p.img('ico_down_arrow_black.gif')+' '+lg('options')+'</a>'
					+ '<div class="optiondiv" id="notebookoptions'+l_id+'">';

                var l_artOptions = '';
				if (l_owner == $p.app.user.id)
				{
					l_artOptions += $p.img('ico_notebooksecurity.gif')+' '+lg('defineArticleAccess')
						+ ' <select name="accesslevel" onchange="$p.notebook.articles.setStatus('+l_id+',this.value,'+l_owner+')">'
						+ '<option value="3"'+(l_status=='3' ? ' selected="selected"' : '')+'>'+lg('notepublicArticle')+'</option>'
						+ '<option value="2"'+(l_status=='2' ? ' selected="selected"' : '')+'>'+lg('notenetworkArticle')+'</option>'
						+ '<option value="1"'+(l_status=='1' ? ' selected="selected"' : '')+'>'+lg('noteprivateArticle')+'</option>'
						+ '<option value="D">'+lg('removeArticle')+'</option>'
						+ '</select>'
						+ '<br />';
				}
                else if (vars['userId'] == $p.app.user.id)
                {
                    l_artOptions += '<a href="#" onclick=\'$p.notebook.articles.setStatus('+l_id+',"D",'+l_owner+');return false;\'>'
                        + lg('removeArticle')
                        + '</a>'
                        + '<br />';
                }

//	if(($user_id!=$oArticle["owner"] && $oArticle["articleStatus"]==3) )
				if (l_status > 2)
				{
					l_artOptions += $p.img('ico_get_article.gif')+' '+lg('getInMyNotebook')+' '
						+ ' <select id="notebook_article'+l_id+'_list" name="accesslevel" onchange="return $p.notebook.trackback('+l_id+','+l_owner+', this.value )">'
						+ '<option value="D" >Loading ...</option>'
						+ '</select>';
				}
                
                if (l_artOptions == '')
                {
                    l_s += lg('nooption');
                }
                else
                {
                    l_s += l_artOptions;
                }
				l_s += '</div>'
					+ '</div>'
					+ '</div>';
			}

			//page management
			l_s += '<div class="notebookfooter">'
				+ $p.html.buildPageNavigator('previousPage',(vars['page']==0 ? '' : '$p.notebook.articles.get('+vars['userId']+','+(vars['page']-1)+(vars['search']==indef ? '' : ',"'+vars['search']+'"')+')'),'','nextPage',(l_result.length==11 ? '$p.notebook.articles.get('+vars['userId']+','+(vars['page']+1)+(vars['search']==indef ? '' : ',"'+vars['search']+'"')+')' : ''))
				+ '</div>';
		}

		$p.print('notebook'+vars['userId']+'_content',l_s);
	},
	/*
		$p.notebook.articles.getDetail: load an article
		Parameters:

			v_id - user or group ID
			v_articleId - article Id
			v_page - page the article is located
	*/
	getDetail:function(v_id,v_articleId,v_page,v_type,v_anchor)
	{
		if (v_type==indef) v_type='note';
		$p.app.wait(v_type+'book'+v_id+'_content');

		$p.ajax.call(pep["notebook_xmlarticlesdetail"]+'?artid='+v_articleId,
			{
				'type':'load',
				'callback':
				{
					'function':$p.notebook.articles.displayDetail,
					'variables':
					{
						'id':v_id,
						'articleId':v_articleId,
						'page':v_page,
						'type':v_type,
						'anchor':v_anchor
					}
				}
			}
		);
	},
	/*
		$p.notebook.articles.displayDetail : Display notebook article detail
		Parameters:

			response : XML response
			vars : variables sent
	*/
	displayDetail:function(response,vars)
	{
		var l_result = response.getElementsByTagName('article');
		var l_id = vars['articleId'];

		var l_s = '';
		
		if (l_result.length != 0)
		{
			var l_status = $p.ajax.getVal(l_result[0],"status","str",false,'');
			var l_owner = $p.ajax.getVal(l_result[0],"owner_id","int",false,0);
			var l_type = $p.ajax.getVal(l_result[0],"type","int",false,0);
			var l_title = $p.ajax.getVal(l_result[0],"title","str",false,'.');
			var l_desc = $p.ajax.getVal(l_result[0],"description","str",false,'');
			var l_authorName = $p.ajax.getVal(l_result[0],"longname","str",false,'-');
			var l_authorPicture = $p.ajax.getVal(l_result[0],"picture","str",false,'');
			//specificity for some types
			if (l_type == 6) l_title = l_authorName+' '+lg('is')+' : '+l_title;
			if (l_type == 4) l_desc = $p.notebook.addPortalAddingLink(l_desc,$p.ajax.getVal(l_result[0],"linked_id","int",false,0));
            if (l_type == 3) l_desc = $p.notebook.addWidgetAddingLink(l_desc);

			//article title
			l_s += $p.html.buildTitle(l_title);

			//Article options
			l_s += '<div style="width: 100%;text-align: right;">'
				+ '<< <a href="#" onclick="$p.'+vars['type']+'book.articles.get('+vars['id']+','+vars['page']+')">'+lg("backToArticles")+'</a>'
				+ ' | <a href="#" onclick=\'$p.notebook.addRssWidget("'+__LOCALFOLDER+'notebook/articlecomments_rss.php?id='+vars['id']+'&artid='+l_id+'","","x");return false;\'>'
				+ $p.img('ico_comment.gif',16,16,'','imgmid')+' '
				+ lg("followCommentsRss")
				+ '</a>'
				+ '</div>'
				+ '<br />';

			l_s += '<div class="notebookarticledesc">'
				+ l_desc
				+ '</div>'
				+ '<br />';

			l_s += '<div id="article'+l_id+'" class="notebookarticle'+l_status+'">'
				+ '<br /><div class="notebookarticleinfo">'
				+ '<< <a href="#" onclick="$p.'+vars['type']+'book.articles.get('+vars['id']+','+vars['page']+')">'+lg("backToArticles")+'</a> | '
				/*+ (l_authorPicture == '' ? ''
                                         : '<div class="picture_image_small">'
                                           + '<img src="'+l_authorPicture+'" />'
                                           + '</div>'
                                           + '<div class="picture_frame_white_small"> </div>'
                                           + '<span style="padding-left: 25px"></span>'
                                           //'<img class="picture" src="'+l_authorPicture+'" width="20px" absmiddle="middle" />'
                )*/
				+ ' <a href="#" onclick=\'$p.notebook.open('+l_owner+',"note","'+l_authorName+'",indef,indef,"'+l_authorPicture+'");return false;\'>'+l_authorName+'</a> | '
				+ $p.date.formatDateLong($p.date.convertFromDb($p.ajax.getVal(l_result[0],"pubdate","str",false,'')),true)+' | '
				+ (l_owner == $p.app.user.id ? $p.img('ico_add_article.gif',16,16,'','imgmid')+' <a href="#" onclick=\'$p.notebook.articles.modify('+vars['id']+','+l_id+','+vars['page']+',"'+vars['type']+'");\'>'+lg('lblModify')+'</a> | ' : '')
				+ ($p.ajax.getVal(l_result[0],"tags","str",false,'')=='' ? '' : '<img src="../images/ico_tag.gif" alt="'+lg("tags")+'" align="absmiddle" /> '+$p.ajax.getVal(l_result[0],"tags","str",false,'')+' | ')
				+ '<a id="article'+l_id+'_optionbtn" href="#" onclick=\'$p.notebook.loadOptions('+l_id+','+l_owner+',"'+vars['type']+'",'+vars['id']+');return false;\'>'+$p.img('ico_down_arrow_black.gif')+' '+lg('options')+'</a>'
				+ '<div class="optiondiv" id="notebookoptions'+l_id+'">';
			
            var l_artOptions = '';

            if (l_owner == $p.app.user.id)
			{
				l_artOptions += $p.img('ico_notebooksecurity.gif')+' '+lg('defineArticleAccess')
					+ ' <select name="accesslevel" onchange="$p.notebook.articles.setStatus('+l_id+',this.value,'+l_owner+')">'
					+ '<option value="3"'+(l_status=='3' ? ' selected="selected"' : '')+'>'+lg(vars['type']+'publicArticle')+'</option>'
					+ '<option value="2"'+(l_status=='2' ? ' selected="selected"' : '')+'>'+lg(vars['type']+'networkArticle')+'</option>'
					+ (vars['type']=='note' ? '<option value="1"'+(l_status=='1' ? ' selected="selected"' : '')+'>'+lg('noteprivateArticle')+'</option>' : '')
					+ '<option value="D">'+lg('removeArticle')+'</option>'
					+ '</select>'
					+ '<br />';
			}
            else if (vars['id'] == $p.app.user.id && vars['type'] == 'note')
            {
                l_artOptions += '<a href="#" onclick=\'$p.notebook.articles.setStatus('+l_id+',"D",'+l_owner+');return false;\'>'
                    + lg('removeArticle')
                    + '</a>'
                    + '<br />';
            }

			if (l_status > 2)
			{
				l_artOptions += $p.img('ico_get_article.gif')+' '+lg('getInMyNotebook')+' '
					+ ' <select id="'+vars['type']+'book_article'+l_id+'_list" name="accesslevel" onchange="return $p.notebook.trackback('+l_id+','+l_owner+', this.value )">'
					+ '<option value="D" >== '+lg('selectBook')+' ==</option>'
					+ (l_owner==$p.app.user.id ? '' : '<option value="id_'+l_owner+'" > '+lg("myNotebook")+' </option>')
					+ '</select>';
			}

            if (l_artOptions == '')
            {
                l_s += lg('nooption');
            }
            else
            {
                l_s += l_artOptions;
            }

			l_s += '</div>'
				+ '</div>'
				+ '</div>'
				+ '<br />';

			//documents
			l_s+= '<a name="documents"></a>'
				+ $p.html.buildTitle(lg('documents'))
			var l_documents = response.getElementsByTagName('document');
			if (l_documents.length == 0)
			{
				l_s += lg('noDocument');
			}
			else
			{
				for (var i = 0;i < l_documents.length;i++)
				{
					l_s += $p.notebook.document.displayItem($p.ajax.getVal(l_documents[i],"title","str",false,''),$p.ajax.getVal(l_documents[i],"link","str",false,''),$p.ajax.getVal(l_documents[i],"size","str",false,''))
						+ '<br />';
				}
			}
			l_s += '<br />';

			//trackbacks
			var l_trackback = response.getElementsByTagName('trackback');
			if (l_trackback.length != 0)
			{
				l_s += '<a name="trackbacks"></a>'
					+ $p.html.buildTitle(lg('theyTakeThisArticle'));
				for (var i = 0;i < l_trackback.length;i++)
				{
					var l_trackId = $p.ajax.getVal(l_trackback[i],'id','int',false,0);
					var l_trackName = $p.ajax.getVal(l_trackback[i],"name","str",false,'');
					var l_trackType = $p.ajax.getVal(l_trackback[i],"type","str",false,'');

					l_s += $p.img((l_trackType=='note' ? 'ico_myaccount.gif' : 'mynetwork.gif'),indef,indef,'','imgmid')
						+ ' <a href="#" onclick=\'$p.notebook.open('+l_trackId+',"'+l_trackType+'","'+l_trackName+'"'+(l_trackType=='note' ? ',indef,indef,"'+$p.ajax.getVal(l_trackback[i],"picture","str",false,'')+'"' : '')+');return false;\'>'+l_trackName+'</a> &nbsp; ';
				}
				l_s += '<br />';
			}

			//comments
			var l_comment = response.getElementsByTagName('comment');
			l_s += '<a name="comments"></a><div id="notebook'+l_id+'_comments">'
				+ $p.html.buildTitle(lg('comments'));

			if (l_comment.length != 0)
			{
				for (var i = 0;i < l_comment.length;i++)
				{
					var l_commentId = $p.ajax.getVal(l_comment[i],'id','int',false,0);
					var l_commentAuthorId = $p.ajax.getVal(l_comment[i],'userid','int',false,0);
					var l_commentAuthorName = $p.ajax.getVal(l_comment[i],'longname','str',false,'');
					var l_commentPicture = $p.ajax.getVal(l_comment[i],"picture","str",false,'');
					var l_commentMessage = $p.ajax.getVal(l_comment[i],"message","str",false,'');
					var l_commentDate = $p.date.convertFromDb($p.ajax.getVal(l_comment[i],'pubdate','str',false,''));

					l_s += $p.notebook.articles.comment.build(l_commentId,l_commentAuthorId,l_commentAuthorName,l_commentPicture,l_commentMessage,l_commentDate);
				}
			}
			l_s += '</div>';

			//comment form
			l_s += '<br />'
				+ '<div class="notebookcommentform">'
				+ '<form name="notebook'+l_id+'_newcomment" onsubmit="return $p.notebook.articles.comment.save(this,'+l_id+','+vars['id']+')">'
				+ '<h2>'+lg("addNewComment")+' :</h2>'
				+ '<textarea name="message" rows="7" style="width: 100%"></textarea>'
				+ '<br /><br />'
				+ '<input type="submit" value="'+lg('lblBtnSend')+'" />'
				+ '</form>'
				+ '</div>';
		}
		$p.print(vars['type']+'book'+vars['id']+'_content',l_s);

		//manage anchors
		if (vars['anchor'] != indef)
		{
			$p.url.goToAnchor(vars['anchor']);
		}		
	},
	/*
		$p.notebook.articles.write: display form to add an article
		Parameters:

			v_id - user or group ID
			v_type - notebook type (group or note)
			v_artId - article ID (if article modification)
			v_title - article title (if article modification)
			v_tags - article tags (if article modification)
			v_access - article access  (if article modification)
			v_description - article description (if article modification)
			v_documents - documents list hash
			
	*/
	write:function(v_id,v_type,v_artId,v_title,v_tags,v_access,v_description,v_documents)
	{
		if (v_type==indef) v_type='note';
		
		var l_validBtn='<input type="submit" value="'+(v_artId==indef ? lg('submit') : lg('lblModify'))+'" />';
		var l_backBtn='<< <a href="#" onclick="$p.'+v_type+'book.selectMenuOption('+v_id+',2)">'+lg("backToArticles")+'</a>';
		
		var l_s = $p.html.buildTitle(lg('writeArticle'))
			+ '<form onsubmit=\'$p.forms.disableAllButton(this);$p.notebook.articles.save('+v_id+',this,"'+v_type+'");return false;\'>'
			+ l_backBtn
			+ ' | '
			+ l_validBtn
			+ '<br /><br />'
			+ '<input type="hidden" name="artid" value="'+(v_artId==indef ? '0' : v_artId)+'" />'
			+ '<b>'+lg('title')+'</b><br />'
			+ '<input type="text" name="title" value="'+(v_artId==indef ? '' : v_title)+'" style="width: 400px;" maxlength="199" />'
			+ '<br /><br />'
			+ '<b>'+lg('keywords')+'</b> '+(__restrictOnExistingTags == true ? '' : tooltip("helpTagsArticle"))+'<br />'
			+ '<input type="text" id="newarticletags" name="kw" value="'+(v_artId==indef ? '' : v_tags)+'" style="width: 400px;" maxlength="199" onkeyup=\'$p.tags.autocompletion.get("newarticletags")\' onfocus=\'$p.tags.selectBox.build(this)\' onblur="$p.tags.autocompletion.hide()" />'
			+ '<input type="hidden" name="kwformated" value="" />'
			+ '<br /><br />'
			+ '<b>'+lg('defineArticleAccess')+'</b><br />'
			+ '<select name="access" style="width: 400px;">'
			+ '	<option value="3"'+(v_access == 3 ? ' selected="selected"' : '')+'>'+lg(v_type+"publicArticle")+'</option>'
			+ '	<option value="2"'+(v_access == 2 ? ' selected="selected"' : '')+'>'+lg(v_type+"networkArticle")+'</option>'
			+ (v_type=='note' ? '	<option value="1"'+(v_access==1 ? ' selected="selected"' : '')+'>'+lg("noteprivateArticle")+'</option>' : '')
			+ '</select>'
			+ '<br /><br />'
			+ '<b>'+lg("description")+'</b>'
			+ '<br />'
			+ '<textarea name="desc" id="'+v_type+'book'+v_id+'_desc" style="width: 400px;height: 500px">'+(v_artId==indef ? '' : v_description)+'</textarea>'
			+ '<br /><br />'
			+ '<b>'+lg('documents')+'</b>'
			+ '<div id="'+v_type+'book'+v_id+'_documents">'
			+ ((v_documents == indef || v_documents.length == 0) ? '' : $p.notebook.document.displayList(v_documents))
			+ '</div>'
			+ '<div id="'+v_type+'book'+v_id+'_addadocument"></div>'
			+ $p.img('ico_add_document.gif',16,16,'','imgmid')
			+ ' <a href="#" onclick=\'$p.notebook.document.callAddForm('+v_id+',"'+v_type+'");return false;\'>'+lg('addADocument')+'</a>'
			+ '<br /><br /><br />'
			+ l_backBtn
			+ ' | '
	        + l_validBtn
			+ '</form>';

		$p.print(v_type+'book'+v_id+'_content',l_s);

		$p.plugin.tools.initializeFckEditor(v_type+'book'+v_id+'_desc',600);
	},
	/*
		$p.notebook.articles.save: save article creation/modification
		Parameters:

			v_id - user or group ID
			v_form - article adding/modification form object
			v_type - notebook type (group or note)
	*/
	save:function(v_id,v_form,v_type)
	{
		//force FCK to take in account recent changes
		$p.plugin.tools.forceFckEditorSaving(v_type+'book'+v_id+'_desc');

		//format tags
		v_form.kwformated.value = $p.string.formatForSearch($p.tags.formatList(v_form.kw.value));
		v_form.kw.value = $p.tags.formatList(v_form.kw.value);
		//get Documents
		var l_inc = 0;
		var l_documents = '';
		while (v_form.elements['filename'+l_inc])
		{
			l_documents	+= '&fn'+l_inc+'='+v_form.elements['filename'+l_inc].value
						+ '&fl'+l_inc+'='+v_form.elements['filelink'+l_inc].value
						+ '&fs'+l_inc+'='+v_form.elements['filesize'+l_inc].value;
			l_inc++;
		}
		//control fields
        if (v_form.title.value=='')
		{
            alert(lg('mustSpecifyTitle'));
			return false;
        }
		//save data
		$p.ajax.call(pep["notebook_scrarticlemodifyadd"],
			{
				'type':'execute',
				'variables':'artid='+v_form.artid.value+(v_type=='group' ? '&gid='+v_id :'')+'&title='+$p.string.esc(v_form.title.value)+'&desc='+$p.string.esc(v_form.desc.value)+'&kw='+$p.string.esc(v_form.kw.value)+'&kwformated='+v_form.kwformated.value+'&access='+v_form.access.value+l_documents,
				'callback':
				{
					'function':$p.notebook.articles.validSaving,
					'variables':
					{
						'id':v_id,
						'type':v_type
					}
				}
			}
		);
		return false;
	},
	validSaving:function(v_fctvars)
	{
		if (v_fctvars['type']=='note')
		{
			$p.notebook.selectMenuOption(v_fctvars['id'],2);
		}
		else
		{
			$p.groupbook.selectMenuOption(v_fctvars['id'],2);
		}
	},
	/*
		$p.notebook.articles.setStatus : set access type to notebook articles
		parameters:

			v_id: article id
			v_status : new status
                                v_ownerId : article owner id
	*/
	setStatus: function(v_id,v_status,v_ownerId)
	{
		if (v_status == "D")
		{
			var response = confirm(lg("msgArchSup"));
			if (response != 1) return false;
		}
		if (v_status != "D" || (v_status == "D" && response == 1))
		{
			$p.ajax.call(pep["scr_notebook_changearticlestatus"],
				{
					'type':'execute',
					'variables':'id='+v_id+'&status='+v_status+'&myarticle='+(v_ownerId == $p.app.user.id ? '1' : '0'),
					'alarm':true,
					'forceExecution':true,
					'callback':
					{
						'function':$p.notebook.articles.setStatusApplied
					}
				}
			);
		}
	},
	/*
		$p.notebook.articles.setStatusApplied :
		input : xml file return
	*/
	setStatusApplied:function(v_ret)
	{
		$p.app.alert.show(lg("modificationApplied"));
		var l_ret = v_ret.split("_");
		navClass("article"+l_ret[0],"notebookarticle"+l_ret[1]);
	},
	/*
		$p.notebook.articles.modify: load an article information to modify it
		Parameters:

			v_id - user or group ID
			v_articleId - article Id
			v_page - page the article is located
			v_type - notebook type (note or group)
	*/
	modify:function(v_id,v_articleId,v_page,v_type)
	{
		$p.app.wait(v_type+'book'+v_id+'_content');

		$p.ajax.call(pep["notebook_xmlarticlesdetail"]+'?artid='+v_articleId,
			{
				'type':'load',
				'callback':
				{
					'function':$p.notebook.articles.fillModifyForm,
					'variables':
					{
						'id':v_id,
						'articleId':v_articleId,
						'page':v_page,
						'type':v_type
					}
				}
			}
		);
	},
	/*
		$p.notebook.articles.fillModifyForm : Fill the article modification form
		Parameters:

			response : XML response
			vars : variables sent
	*/
	fillModifyForm:function(response,vars)
	{
		var l_result = response.getElementsByTagName('article');

		var l_title = $p.ajax.getVal(l_result[0],"title","str",false,'.');
		var l_tags = $p.ajax.getVal(l_result[0],"tags","str",false,'');
		var l_status = $p.ajax.getVal(l_result[0],"status","str",false,'');
		var l_desc = $p.ajax.getVal(l_result[0],"description","str",false,'');

		var l_documents = l_result[0].getElementsByTagName('document');
		var l_documentsArray = [];
		for (var i = 0;i < l_documents.length;i++)
		{
			l_documentsArray.push({'notebookType':vars['type'],'notebookId':vars['id'],'name':$p.ajax.getVal(l_documents[i],"title","str",false,''),'link':$p.ajax.getVal(l_documents[i],"link","str",false,''),'size':$p.ajax.getVal(l_documents[i],"size","str",false,'')});
		}

		if (l_result.length != 0)
		{
			$p.notebook.articles.write(vars['id'],vars['type'],vars['articleId'],l_title,l_tags,l_status,l_desc,l_documentsArray);
		}
	}
}
$p.notebook.articles.comment={
	/*
		$p.notebook.articles.comment.build: build comment box
		Parameters:

			v_commentId - Comment ID
			v_commentAuthorId - comment author ID
			v_commentAuthorName - Comment Author long name
			v_commentPicture - comment picture
			v_commentMessage - comment message
			v_commentDate - comment date
	*/
	build:function(v_commentId,v_commentAuthorId,v_commentAuthorName,v_commentPicture,v_commentMessage,v_commentDate)
	{
		var l_s = '<div id="comment'+v_commentId+'" class="notebookcomment">'
            + '<div style="width: 50px;float: left;margin-right: 4px;">'
            + (v_commentPicture=='' ? '' : '<img src="'+v_commentPicture+'" width="40" />')
            + '</div>'
			+ lg("by")+' <a href="#" onclick=\'$p.notebook.open('+v_commentAuthorId+',"note","'+v_commentAuthorName+'",indef,indef,"'+v_commentPicture+'");return false;\'>'+v_commentAuthorName+'</a>'
			+ ', '+$p.date.formatDateLong(v_commentDate,true)
			+ (v_commentAuthorId==$p.app.user.id ? ' | <a href="#" onclick="return $p.notebook.articles.comment.remove('+v_commentId+')">'+lg("suppress")+'</a>' : '')
			+ '<br /><br />'
			+ $p.string.textToHtml(v_commentMessage)
            + '</div>';
		return l_s;
	},
	/*
		$p.notebook.articles.comment.save: save article comment
		Parameters:

			v_form - form object
			v_articleId - ID of the article
			v_userId - user ID
	*/
	save:function(v_form,v_articleId,v_userId)
	{
		$p.ajax.call(pep["notebook_scrsavecomment"],
			{
				'type':'execute',
				'variables':'artid='+v_articleId+'&uid='+v_userId+'&message='+v_form.message.value,
				'callback':
				{
					'function':$p.notebook.articles.comment.confirmSave,
					'variables':
					{
						'message':v_form.message.value,
						'articleId':v_articleId
					}
				}
			}
		);
		return false;
	},
	confirmSave:function(v_commentId,fctvars)
	{
		//display new comment
		$p.print('notebook'+fctvars['articleId']+'_comments',$p.notebook.articles.comment.build(v_commentId,$p.app.user.id,$p.app.user.name,'',fctvars['message'],new Date()),'bottom');
		//empty comment form
		document.forms['notebook'+fctvars['articleId']+'_newcomment'].message.value='';
	},
	/*
		$p.notebook.articles.comment.remove : remove comment
		inputs
			v_id: comment id
	*/
	remove:function(v_id)
	{
		var response=confirm(lg("readyToSuppressComment"));
		if (response!=1) return false;
		$p.ajax.call(pep["scr_notebook_removecomment"],
			{
				'type':'execute',
				'variables':'id='+v_id,
				'alarm':true,
				'forceExecution':true,
				'callback':
				{
					'function':$p.notebook.articles.comment.confirmRemove
				}
			}
		);

		return false;
	},
	/*
		$p.notebook.articles.comment.confirmRemove : confirm comment suppression
		input : xml file return
	*/
	confirmRemove:function(v_ret)
	{
		if (v_ret!=indef)
		{
			$p.app.alert.show(lg("modificationApplied"));
			navShow("comment"+v_ret,"none");
		}
	}
};
$p.notebook.profile={
	desc:"",
	/*
		$p.notebook.profile.get : Get notebook user profile
		Parameters:

			v_userId : user ID
	*/
	get:function(v_userId)
	{
		$p.app.wait('notebook'+v_userId+'_content');

		$p.ajax.call(pep["xmlnetwork_completeInfos"]+'?id='+v_userId,
			{
				'type':'load',
				'callback':
				{
					'function':$p.notebook.profile.display,
					'variables':
					{
						'userId':v_userId
					}
				}
			}
		);
	},
	/*
		$p.notebook.profile.display : Display notebook user profile
		Parameters:

			response : XML response
			vars : variables sent
	*/
	display:function(response,vars)
	{
		//general user info
		var v_id = $p.ajax.getVal(response,'id','int',false,0);
		var picture = $p.ajax.getVal(response,'picture','str',false,'');
		var description = $p.ajax.getVal(response,'description','str',false,'');
		var username = $p.ajax.getVal(response,'username','str',false,'');
		var longname = $p.ajax.getVal(response,'longname','str',false,'');
		var keywords = $p.ajax.getVal(response,'keywords','str',false,'');
		var networknb = $p.ajax.getVal(response,'innetwork','int',false,0);
		var mydescription = $p.ajax.getVal(response,'mydescription','str',false,'');
        
		//get keywords i set for this user
		var l_kw=[],l_result2=response.getElementsByTagName("mykeywords");
		for (var i=0;i<l_result2.length;i++)
		{
			l_kw.push(l_result2[i].firstChild.nodeValue);
		}
		if (l_kw.length==0)	l_kw.push('');			
		
		//popup content
		var l_s=''
			+$p.html.buildTitle(lg('publicInfo'))
			+(checkEmail(username) ? '<p><b>'+lg('lblEmail')+': </b> '+username+'</p>' :'')
			+'<p><b>'+lg('Name')+': </b> '+longname+'</p>'
			+'<p><b>'+lg('tags')+': </b>'+keywords+'</p>'
			+'<p><b>'+lg('desc')+': </b>'+description+'</p>';
          
        
		l_s+=$p.network.dashboard.buildCriteria(response);

		if (networknb==1)   {
			l_s+=$p.html.buildTitle(lg('privateInfo'))
				+'<p><b>'+lg('myTags')+' :</b>'+l_kw.join(",")+' <a href="#" onclick="$p.network.card.load('+v_id+')">'+lg('modify')+'</a></p>'
				+'<p><b>'+lg('description')+' :</b>'+mydescription+' <a href="#" onclick="$p.network.card.load('+v_id+')">'+lg('modify')+'</a></p>';
		}

		if (networknb==0)   {
			l_s+='<center><input type="button" class="btn" onclick="return $p.network.card.load('+v_id+',true)" value="'+lg('addToMyNetwork')+'" /></center>';
		}

		$p.print('notebook'+vars['userId']+'_content',l_s);
	},
	/*
		$p.notebook.profile.getDesc : display my profile formated description 
		inputs : define if the description is truncated or not
	*/
	getDesc:function(v_trunc)
	{
		var l_desc=(v_trunc && $p.notebook.profile.desc.length>150)?$p.notebook.profile.desc.substr(0,150)+" ...[<a href='#' onclick='$p.notebook.profile.getDesc(false);return false;'>"+lg("readMore")+"</a>]":$p.notebook.profile.desc;
		$p.print("notebookdesc",l_desc);
	}
}
$p.notebook.network={
	/*
		$p.notebook.network.get : Get user network
		Parameters:

			v_userId : user ID
	*/
	get:function(v_userId,v_page)
	{
		$p.app.wait('notebook'+v_userId+'_content');

		$p.ajax.call(pep["notebook_xmlusernetwork"]+"?id="+v_userId+"&s="+(v_page*20),
			{
				'type':'load',
				'callback':
				{
					'function':$p.notebook.network.display,
					'variables':
					{
						'userId':v_userId,
						'page':v_page
					}
				}
			}
		);
	},
	/*
		$p.notebook.network.display : Display user network
		Parameters:

			response : XML response
			vars : variables sent
	*/
	display:function(response,vars)
	{
		var l_s = $p.html.buildTitle(lg('network'));

		var l_result = response.getElementsByTagName("user");

		if (l_result.length == 0)
		{
			l_s += lg('noFriends');
		}
		else
		{
			l_s += '<ul class="card-outer">';
			for (var i = 0;i < $p.min(l_result.length,20);i++)
			{
				l_id = $p.ajax.getVal(l_result[i],"id","int",false,0);
				var l_picture = $p.ajax.getVal(l_result[i],"picture","str",false,"");
				if (l_picture == "")
                    l_picture = "../images/nopicture.gif";

				l_s += $p.network.dashboard.buildElement(l_id,l_picture,$p.ajax.getVal(l_result[i],"longname","str",false,"..."),$p.ajax.getVal(l_result[i],"username","str",false,"..."),$p.ajax.getVal(l_result[i],"stat","str",false,"")+' -'+$p.date.formatDelai($p.date.delayFromNow($p.date.convertFromDb($p.ajax.getVal(l_result[i],"statdate","str",false,"")))),$p.ajax.getVal(l_result[i],"description","str",false,""),$p.ajax.getVal(l_result[i],"keywords","str",false,""),'',indef,$p.chat.computeActivity($p.ajax.getVal(l_result[i],'activity','str',false,''),$p.ajax.getVal(l_result[i],'lastconndate','str',false,''),$p.ajax.getVal(l_result[i],'dbdate','str',false,'')));

			}
			l_s += '</ul>'
				+ $p.html.buildPageNavigator('previous',(vars['page']==0 ? '' : '$p.notebook.network.get('+vars['userId']+','+(vars['page']-1)+')'),'','next',(l_result.length<21 ? '' : '$p.notebook.network.get('+vars['userId']+','+(vars['page']+1)+')'));
		}

		$p.print('notebook'+vars['userId']+'_content',l_s);
	}
}
$p.notebook.contributions={
	/*
		$p.notebook.contributions.get : Get user contributions
		Parameters:

			v_userId : user ID
	*/
	get:function(v_userId)
	{
		$p.app.wait('notebook'+v_userId+'_content');

		$p.ajax.call(pep["xmlnetwork_usersummary"]+"?id="+v_userId,
			{
				'type':'load',
				'callback':
				{
					'function':$p.notebook.contributions.display,
					'variables':
					{
						'userId':v_userId
					}
				}
			}
		);
	},
	/*
		$p.notebook.contributions.display : Display user contribiution
		Parameters:

			response : XML response
			vars : variables sent
	*/
	display:function(response,vars)
	{
		var l_s=$p.html.buildTitle(lg('contributions'));

		var l_result=response.getElementsByTagName("update"),l_date,l_previousDate,l_currentDate;
		if (l_result.length>0)
		{
			for (var i=0;i<l_result.length;i++)
			{
				var l_date=$p.ajax.getVal(l_result[i],"pubdate","str",false,"");
				l_currentDate=$p.date.formatDateLong($p.date.convertFromDb(l_date));
				if (l_previousDate!=l_currentDate)
				{
					l_s+="<div class='subtitle' style='clear: left;margin: 6px 0 4px 0;'>"+l_currentDate+"</div>";
					l_previousDate=l_currentDate;
				}
				var l_id=$p.ajax.getVal(l_result[i],"id","int",false,0);
				var l_type=$p.ajax.getVal(l_result[i],"type","int",false,2);
				var l_link=$p.ajax.getVal(l_result[i],"link","str",false,"");
				
				l_s+=$p.network.information.buildItem(vars['userId'],l_type,l_link,$p.ajax.getVal(l_result[i],"title","str",false,"..."),indef,$p.ajax.getVal(l_result[i],"long_name","str",false,"???"),l_date);
			}
		} else 
		{
			l_s+="<b>"+lg("noNews")+"</b>";
			l_s+="</ul><div style='clear: both;float: none;'>";
			l_s+="<div style='text-align: center;background: #c6c3c6;height: 22px;margin-top: 15px;padding-top: 3px;'></div>";
		}

		$p.print('notebook'+vars['userId']+'_content',l_s);
	}
}
$p.notebook.document={
	notebookType:'indef',
	notebookId:0,
	items:[],
	/*
		$p.notebook.document.callAddForm : Display document add form
		Parameters:

			v_id : notebook ID
			v_type : notebook type
	*/
	callAddForm: function(v_id, v_type)
	{
		this.notebookType = v_type;
		this.notebookId = v_id;

		$p.print(v_type+'book'+v_id+'_addadocument','<iframe src="../includes/upload_component.inc.php?subfolder=document&fct=$p.notebook.document.formCallback&closeafter=yes" width="600" height="30" frameborder="0"></iframe>');
	},
	/*
		$p.notebook.document.formCallback : treat the return of the upload form
		Parameters:
			v_type : return type
			v_origFile : original file name
			v_newFile : new file name
			v_size : file size
	*/
	formCallback: function(v_type,v_origFile,v_newFile,v_size)
	{
		if (v_type == 'cancel')
		{
			$p.print(this.notebookType+'book'+this.notebookId+'_addadocument','');
			return;
		}
		if (v_type == 'add')
		{
			v_size = parseInt(v_size/1000,10)+1;

			//display the file
			this.items.push({'notebookType':$p.notebook.document.notebookType,'notebookId':$p.notebook.document.notebookId,'name':v_origFile,'link':v_newFile,'size':v_size});
			//display documents list
			this.list();
		}
	},
	/*
		$p.notebook.document.list : display list of documents attached
		Parameters:

			v_origFile : original file name
			v_newFile : new file name
	*/
	list: function()
	{
		$p.print(this.notebookType+'book'+this.notebookId+'_documents',this.getList());
	},
	getList: function()
	{
		var l_s = '';
		var l_inc=0;
		for (var i = 0;i < this.items.length; i++)
		{
			if (this.items[i].notebookId == this.notebookId && this.items[i].notebookType == this.notebookType)
			{
				l_s+= this.displayItem(this.items[i].name,this.items[i].link,this.items[i].size,i)
					+ '<input type="hidden" name="filename'+l_inc+'" value="'+this.items[i].name+'" />' 
					+ '<input type="hidden" name="filelink'+l_inc+'" value="'+this.items[i].link+'" />'
					+ '<input type="hidden" name="filesize'+l_inc+'" value="'+this.items[i].size+'" />'
					+ '<br />';
				l_inc++;
			}
		}
		return l_s;
	},
	/*
		$p.notebook.document.displayItem : display a document
		Parameters:

			v_documents : documents hash
	*/
	displayItem: function(v_name,v_link,v_size,v_itemId)
	{
		var l_s = this.getIcon(v_name)
			+ ' <a href="../upload/document/'+v_link+'">'
			+ v_name
			+ ' ('+v_size+' Ko)';
		if (v_itemId != indef)
		{
			l_s+= '</a> '
			 + '<a href="#" onclick="$p.notebook.document.remove('+v_itemId+');return false;">'
			 + $p.img('ico_close.gif',12,11,'','imgmid')
			 + '</a>';
		}
		return l_s;
	},
	/*
		$p.notebook.document.displayList : return list of documents attached
		Parameters:

			v_documents : documents hash
	*/
	displayList: function(v_documents)
	{
		this.notebookType = v_documents[0].notebookType;
		this.notebookId = v_documents[0].notebookId;

		this.clearList(this.notebookType,this.notebookId);

		for (var i = 0;i < v_documents.length; i++)
		{
			this.items.push({'notebookType':v_documents[i].notebookType,'notebookId':v_documents[i].notebookId,'name':v_documents[i].name,'link':v_documents[i].link,'size':v_documents[i].size});
		}

		return this.getList();
	},
	getIcon: function(v_file)
	{
		var l_ext=v_file.slice(-3);
		return '<img src="../images/file_'+l_ext+'.gif" align="absmiddle" />';
	},
	/*
		$p.notebook.document.remove : Remove a document
		Parameters:

			v_arrayPosition : position of the document in the array
	*/
	remove: function(v_arrayPosition)
	{
		this.items.splice(v_arrayPosition,1);
		this.list();
	},
	/*
		$p.notebook.document.clearList : Clear the notebook list for a notebook
		Parameters:

			v_type : notebook type
			v_id : notebook id
	*/
	clearList: function(v_type,v_id)
	{
		var l_itemToClear = [];
		for (var i = 0;i < this.items.length;i--)
		{
			if (this.items[i].notebookType == v_type && this.items[i].notebookId)
			{
				l_itemToClear.push(i);
			}
		}
		for (var i = (l_itemToClear.length - 1);i >= 0;i--)
		{
			this.items.splice(l_itemToClear[i],1);
		}
	}
}

$p.help={
	menu:new Array({"id":1,"title":"lblHelp","icon":"ico_help.gif","fct":"","isLink":false}),
	open:function()
	{
		$p.plugin.open();
		$p.plugin.init(lg("lblHelp"),'help');
		$p.plugin.application="help";
		$p.plugin.menu($p.help.menu,1);
		$p.app.setState("$p.help.open()");
		
		var l_s="<div class='subtitle'>"+lg("guides")+"</div><br />";
		l_s+="<a href='../docs/"+__lang+"/guide_de_l_utilisateur.pdf' target='_blank'>"+lg("userGuide")+"</a><br />"
		l_s+="<br /><div class='subtitle'>"+lg("contacts")+"</div><br />";
		l_s+="<a href='mailto:"+__SUPPORTEMAIL+"'>"+__SUPPORTEMAIL+"</a><br />";
		l_s+="<br /><div class='subtitle'>"+lg("credits")+"</div><br />";
		l_s+="<br />icons : FamFamFam<br />";
		l_s+="<br />&copy; Portaneo";
		$p.plugin.content(l_s);
	}
}

// Manage the users' messages
function messageObj(id,title,description,status,sender,senddate){
	this.id=id;
	this.title=title;
	this.description=description;
	this.status=status;
	this.sender=sender;
	this.senddate=senddate;
};

$p.msg={
	shown:false,
	items:[],
	nbPerPage:10,
	sel:0,
	nbUnread:-1,
	init:function(){
		$p.plugin.open();
		//$p.app.tabs.create(indef,false,false,-1);
		var l_s="<div class='addonbar'>"+lg("receivedMessages")+" | <a href=''>"+lg("sentMessage")+"</a> | <a href='#' onclick='$p.msg.write();'>"+$p.img("ico_mail_write.gif",16,16,"","imgmid")+" "+lg("sendANewMessage")+"</a> | <a href='#' onclick='$p.msg.load()'>"+$p.img("ico_refresh.gif",12,11,"","imgmid")+" "+lg("refresh")+"</a></div><br />";
		l_s+="<table width='100%'><tr><td id='messageslist' width='500' valign='top'>";
		l_s+="</td><td valign='top'>";
		l_s+="<a href='#' onclick=\"$p.msg.write('','Fw: '+$p.msg.items[$p.msg.sel].title,'================================'+$p.msg.items[$p.msg.sel].description)\">"+$p.img("ico_mail_transfert.gif",16,16,"","imgmid")+" "+lg("transfert")+"</a> | <a href='#' onclick=\"$p.msg.write($p.msg.items[$p.msg.sel].sender,'Re: '+$p.msg.items[$p.msg.sel].title,'================================'+$p.msg.items[$p.msg.sel].description)\">"+$p.img("ico_mail_reply.gif",16,16,"","imgmid")+" "+lg("reply")+"</a> | <a href='#' onclick='$p.msg.suppress()'>"+$p.img("ico_suppress.gif",14,14,"","imgmid")+" "+lg("delete")+"</a> | <a href=''>"+lg("getByEmail")+"</a><br />";
		l_s+="<br /><div width='100%' id='messagereadbox'>&nbsp;</div>";
		l_s+="</td></tr></table>";

		l_s+="</td></tr></table>";
		$p.print("messages",l_s);
		navShow("messages","block");
		navWait("messageslist");
		$p.msg.shown=true;
		$p.msg.load(false);
	},
	load:function(v_alert,v_start){
		if (v_start==indef) v_start=0;
		$p.ajax.call(pep["xmlmessages"]+'?start='+v_start+'&nb='+$p.msg.nbPerPage,
			{
				'type':'load',
				'callback':
				{
					'function':$p.msg.get
				}
			}
		);
	},
	get:function(response,vars){
		var i=0;
		$p.msg.items.length=0;
		while (response.getElementsByTagName("message")[i]){
			$p.msg.items.push(new messageObj($p.ajax.getVal(response.getElementsByTagName("message")[i],"id","int",false,0),$p.ajax.getVal(response.getElementsByTagName("message")[i],"title","str",false,"=="),$p.ajax.getVal(response.getElementsByTagName("message")[i],"description","str",false,""),$p.ajax.getVal(response.getElementsByTagName("message")[i],"status","str",false,"U"),$p.ajax.getVal(response.getElementsByTagName("message")[i],"sender","str",false,"-"),$p.ajax.getVal(response.getElementsByTagName("message")[i],"senddate","str",false,"")));
			i++;
		}
		$p.msg.display();
		$p.msg.read(0);
	},
	display:function(){
		var l_s="<br /><table width='100%'>";
		for (var i=0;i<$p.msg.items.length;i++){
			if ($p.msg.items[i].status=="U"){
				l_s+="<tr><td valign='top' width='22'>"+$p.img("ico_mail_unread.gif",16,16)+"</td><td class='"+($p.msg.sel==i?"selmessage":"message")+"'><b><a href='#' onclick='$p.msg.read("+i+")'>"+$p.msg.items[i].title+"</a></b>";
			} else {
				l_s+="<tr><td valign='top' width='22'>"+$p.img("ico_mail_read.gif",16,16)+"</td><td class='"+($p.msg.sel==i?"selmessage":"message")+"'><a href='#' onclick='$p.msg.read("+i+")'>"+$p.msg.items[i].title+"</a>";
			}
			l_s+="<br />sent the "+$p.msg.items[i].senddate+" by "+$p.msg.items[i].sender+"</td></tr>";
		}
		l_s+="</table>";
		if (i==0) l_s+="<i>"+lg("lblMsgNone")+"</i>";
		$p.print("messageslist",l_s);
		//$p.msg.getNb();
	},
	read:function(v_i){
		$p.msg.sel=v_i;
		if ($p.msg.items[v_i].status=="U"){
			$p.msg.items[v_i].status="R";
			//record read status in DB
			$p.ajax.call(pep["scr_readmessage"],
			{
				'type':'execute',
				'variables':'id='+$p.msg.items[v_i].id
			}
		);
		}
		$p.print("messagereadbox",$p.msg.items[v_i].description);
		//$p.print("messagereadbox","toot");
		$p.msg.display();
	},
	write:function(v_dest,v_title,v_msg){
		$p.plugin.open();
		//$p.app.tabs.create(indef,false,false,-1);
		var l_s="<div class='addonbar'><a href='#' onclick='$p.msg.init()'>"+lg("receivedMessages")+"</a> | <a href=''>"+lg("sentMessage")+"</a> | "+$p.img("ico_mail_write.gif",16,16,"","imgmid")+" "+lg("sendANewMessage")+" | <a href='#' onclick='$p.msg.load()'>"+$p.img("ico_refresh.gif",12,11,"","imgmid")+" "+lg("refresh")+"</a></div><br />";
		l_s+="<table width='100%'><tr><td valign='top'>";
		l_s+="<form name='f' method='post'>"
		l_s+=lg("sendTo")+" "+lg("selectedPeopleInTheList")+"<br /><input type='text' name='to' size='50' value='"+(v_dest==indef?"":v_dest)+"'/><br /><br />";
		l_s+=lg("title")+"<br /><input type='text' name='title' size='50' value='"+(v_title==indef?"":v_title)+"' /><br /><br />";
		l_s+=lg("message")+"<br /><textarea name='message' cols='50' rows='10'>"+(v_msg==indef?"":v_msg)+"</textarea><br /><br />";
		l_s+="<input type='button' class='btn' value='"+lg("send")+"' />";
		l_s+="</form>";
		l_s+="</td><td id='mailfriends' valign='top' width='500px'>";
		l_s+="</td></tr></table>";
		$p.print("messages",l_s);
		navShow("messages","block");
		navWait("mailfriends");
		$p.msg.shown=true;
		$p.msg.friends();
	},
	getNb:function(){
		$p.ajax.call(pep["xmlnbmessages"],
			{
				'type':'load',
				'callback':
				{
					'function':$p.msg.displayNb
				}
			}
		);
	},
	displayNb:function(response,vars){
		var l_nb=$p.ajax.getVal(response,"nb","int",false,0);
		if ($p.msg.nbUnread>=0 && l_nb>$p.msg.nbUnread && !$p.msg.shown){
			$p.app.alert.show(lg("youHaveReceivedNewMessages"));
		}
		$p.msg.nbUnread=l_nb;
	},
	hide:function(){
		$p.print("messages","");
		navShow("messages","none");
		$p.msg.shown=false;
	},
	suppress:function(v_id){
		if (v_id==indef) v_id=$p.msg.sel;
		var response=confirm(lg("doYouWantToSuppressMessage"));
		if (response==1){
			$p.ajax.call(pep["scr_supmessage"],
				{
					'type':'execute',
					'variables':'id='+$p.msg.items[v_id].id,
					'alarm':false,
					'forceExecution':false,
					'callback':
					{
						'function':$p.msg.load
					}
				}
			);
// a modifier (par recharger a chaque suppression !!)
			//$p.msg.getNb();
		}
	},
	friends:function(){
		$p.ajax.call(pep["xmlfriends"],
			{
				'type':'load',
				'callback':
				{
					'function':$p.msg.displayFriends
				}
			}
		);
	},
	displayFriends:function(response,vars){
		var l_s="";
		if (response.getElementsByTagName("friend")[0]){
			var inc=0;
			while (response.getElementsByTagName("friend")[inc]){
				var l_result=response.getElementsByTagName("friend")[inc];
				l_s+="<a href='#'>"+$p.ajax.getVal(l_result,"name","str",false,"...")+"</a>";
				inc++;
			}
		}
		else {l_s+=lg("noFriends");}
		$p.print("mailfriends",l_s);
	}
};

$p.chat={
	minimized:false,
	/*
		$p.chat.buildPage : Build Chat main page
	*/
	buildPage:function()
	{
		$p.network.init();
		$p.network.buildPageMenu(5);

		var l_s=$p.html.buildTitle(lg('chat'))
			+'<div id="menuchatconnpeople"></div>';
		$p.print('network_content',l_s);
		
		$p.chat.getConnectedPeople();
	},
	/*
		$p.chat.checkActivity : check user activity
	*/
	checkActivity:function()
	{
		var l_oldStatus=$p.app.user.status;

		if (l_oldStatus!='b')
		{
			if ($p.app.inactivityTime<=12)
			{
				$p.app.user.status='o'; //online status
			}
			else
			{
				$p.app.user.status='a'; //away status
			}
		}

		if (l_oldStatus!=$p.app.user.status) $p.chat.displayStatus();

		$p.chat.getNotifications((l_oldStatus!=$p.app.user.status || $p.app.counter.step%6==5)?'true':'false');
	},
	/*
		$p.chat.getNotifications : get Notifications from DB
	*/
	getNotifications:function(sendActivity)
	{
		//get the opened discussion
		l_writingChat=[];
		l_inactiveChat=[];
		for (var i=0;i<$p.chat.discussion.opened.length;i++)
		{
			if ($p.chat.discussion.opened[i].status=='O')
			{
				var l_id=$p.chat.discussion.opened[i].id;

				//write status management
				if (document.forms['chatform'+i].chatInput.value!='' && $p.chat.discussion.opened[i].myStatus!='write')
				{
					l_writingChat.push($p.chat.discussion.opened[i].userid);
					$p.chat.discussion.opened[i].myStatus='write';
				}
				//chat inactivity management
				if (document.forms['chatform'+i].chatInput.value=='' && $p.chat.discussion.opened[i].myStatus=='write')
				{
					//after 4 *10s of inactivity
					if ($p.chat.discussion.opened[i].inactivityStep==3)
					{
						l_inactiveChat.push($p.chat.discussion.opened[i].userid);
						$p.chat.discussion.opened[i].myStatus='none';
					}
					else
					{
						$p.chat.discussion.opened[i].inactivityStep++;
					}
				}
			}
		}

		$p.ajax.call(pep["scr_chat_activity"]+(sendActivity?'?act='+$p.app.user.status:'?noact=1')+(l_writingChat.length==0?'':'&writing='+l_writingChat.join(','))+(l_inactiveChat.length==0?'':'&inac='+l_inactiveChat.join(',')),
			{
				'type':'load',
				'callback':
				{
					'function':$p.chat.treatNotifications
				}
			}
		);
	},
	treatNotifications: function(response,vars)
	{
		//check new discussions
		var l_newChats = response.getElementsByTagName('newchat');
		for (var i = 0;i < l_newChats.length;i++)
		{
			$p.chat.discussion.open($p.ajax.getVal(l_newChats[i],'id','int',false,0),$p.ajax.getVal(l_newChats[i],'userid','int',false,0),$p.ajax.getVal(l_newChats[i],'name','str',false,'?'));
		}

		var l_messages = response.getElementsByTagName('message');
		for (var i = 0;i < l_messages.length;i++)
		{
			$p.chat.discussion.write($p.ajax.getVal(l_messages[i],'chatid','int',false,0),$p.ajax.getVal(l_messages[i],'content','str',false,''));
		}

		if (l_messages.length > 0)
        {
            $p.navigator.sound(); // sound if new message
        }

		//check writing in chat
		var l_writing = response.getElementsByTagName('writing');
		for (var i = 0;i < l_writing.length;i++)
		{
			var l_status = $p.ajax.getVal(l_writing[i],'type','str',false,'');
			$p.chat.discussion.status($p.chat.discussion.getId($p.ajax.getVal(l_writing[i],'userid','int',false,0)),l_status);
		}
	},
	/*
		Function : $p.chat.displayStatus
                                    display user activity
                      Parameters :
                            v_containerId : object id where the status will be placed
	*/
	displayStatus: function(v_containerId)
	{
		if ($(v_containerId) != null)
		{
			$p.print(v_containerId,
                '<a href="#" onclick=\'$p.chat.activitySelection("'+v_containerId+'");return false;\' title="'+lg('activity'+$p.app.user.status)+'" >'
                + $p.img('ico_activity'+$p.app.user.status+'.gif',16,16,lg('activity'+$p.app.user.status),'imgmid')
                + $p.img('ico_down_arrow2.gif',14,20,'','imgmid')+'</a> '
            );
		}
	},
	/*
		Function : $p.chat.activitySelection
                                give the activities selection
                      Parameters :
                            v_containerId : object id where the status is placed
	*/
	activitySelection: function(v_containerId)
	{
		$p.print(v_containerId,
            '<a href="#" onclick=\'$p.chat.setActivity("o","'+v_containerId+'");return false;\' title="'+lg('activityo')+'">'+$p.img('ico_activityo.gif',16,16,lg('activityo'))+'</a> '
            + '<a href="#" onclick=\'$p.chat.setActivity("b","'+v_containerId+'");return false;\' title="'+lg('activityb')+'">'+$p.img('ico_activityb.gif',16,16,lg('activityb'))+'</a> '
            + '<a href="#" onclick=\'$p.chat.setActivity("x","'+v_containerId+'");return false;\' title="'+lg('activityx')+'">'+$p.img('ico_activityx.gif',16,16,lg('activityx'))+'</a>'
        );
	},
	/*
		Function : $p.chat.setActivity
                      Parameters :
                                v_activity : new status
                                v_containerId : object id where the status is placed
	*/
	setActivity: function(v_activity,v_containerId)
	{
		if (v_activity != $p.app.user.status)
		{
			$p.app.user.status=v_activity;
			$p.chat.getNotifications(true);
		}
        $p.chat.displayStatus(v_containerId);
	},
	/*
		$p.chat.computeActivity : compute user activity
		Inputs :
			v_lastActivity : activity status logged in DB
			v_lastconnect : date of the latest user connection
			v_refdate : date of the DB
	*/
	computeActivity:function(v_lastActivity,v_lastconnect_date,v_refdate)
	{
		if (v_lastActivity=='x') return 'x';
		var l_delay=$p.date.convertFromDb(v_refdate)-$p.date.convertFromDb(v_lastconnect_date);
		//if no new status for 2 min => disconnected
		if (l_delay>120000) return 'x';
		return v_lastActivity;
	},
	/*
		$p.chat.getConnectedPeople : get the list of the chat connected people
	*/
	getConnectedPeople:function()
	{
		if ($p.app.user.id==0)	
			$('menuchatconnpeople').set('html',lg('msgNeedToBeConnectedMenu'));
		else	
		{
				$p.ajax.call(pep["xmlnetwork_connected"],
					{
						'type':'load',
						'callback':
						{
							'function':$p.chat.displayConnectedPeople
						}
					}
				);
		}
	},
	displayConnectedPeople:function(response,vars)
	{
		var l_result=response.getElementsByTagName('user'),l_chatNb=0,l_s='<div class="title">'+lg("connectedPeople")+' :</div>';

		//display users
		for (var i=0;i<l_result.length;i++)
		{
			var l_id=$p.ajax.getVal(l_result[i],'id','int',false,0);
			var l_activity=$p.chat.computeActivity($p.ajax.getVal(l_result[i],'activity','str',false,'x'),$p.ajax.getVal(l_result[i],'lastconndate','str',false,''),$p.ajax.getVal(l_result[i],'dbdate','str',false,''));
			if (l_id!=$p.app.user.id && l_activity!='x' && l_activity!='b')
			{
				var l_id=$p.ajax.getVal(l_result[i],'id','int',false,0);
				var l_longname=$p.ajax.getVal(l_result[i],'longname','str',false,'???');
				l_s+='<a href="#" onclick=\'$p.chat.discussion.open(0,'+l_id+',"'+l_longname+'");return false;\'>'+$p.img('ico_activity'+l_activity+'.gif',16,16,lg('activity'+l_activity),'imgmid')+' '+l_longname+'</a><br />';
				l_chatNb++;
			}
		}
		if (l_chatNb==0)
		{
			l_s+=lg('nobodyConnected');
		}

		l_s+='<br /><br />'+$p.img('puce.gif')+'&nbsp;<a href="#" onclick="$p.chat.archive()">'+lg('archives')+'</a>';
		$p.print('menuchatconnpeople',l_s);
	},
	/*
		$p.chat.displayBoxes : display chat area
	*/
	displayArea:function()
	{
		if ($('chatarea')==null)
		{
			var chatObj=new Element('div',
				{
					'id':'chatarea',
					'styles':
					{
						'overflow':'hidden'
					}
				}
			);
			chatObj.injectInside($('menus'));
		}
		else
		{
			navShow('chatarea','block');
		}
	},
	/*
		$p.chat.archive : get chat archive
	*/
	archive:function()
	{
		$p.network.init();
		$p.app.setState("$p.chat.archive()");

		$p.app.wait('network_content');

		$p.chat.getArchive(0);
	},
	getArchive:function(v_page)
	{
		$p.ajax.call(pep["xmlchat_archive"]+'?p='+v_page,
			{
				'type':'load',
				'callback':
				{
					'function':$p.chat.displayArchive,
					'variables':
					{
						'page':v_page
					}
				}
			}
		);
	},
	displayArchive:function(response,vars)
	{
		$p.network.init();
		$p.network.buildPageMenu(7);

		var l_s=$p.html.buildTitle(lg("archives"))
			+'<table width="100%">'
			+'<tr>'
			+'<td width="50%" valign="top">'
			+'<table>';
		var l_result=response.getElementsByTagName("chat");

		if(l_result.length==0){
			l_s+="<b>"+lg('noArchives')+"</b>";
		}
		for (var i=0;i<l_result.length;i++)
		{
			var l_date=$p.date.formatDateShort($p.date.convertFromDb($p.ajax.getVal(l_result[i],'pubdate','str',false,'')));
			var l_username=$p.ajax.getVal(l_result[i],'username','str',false,'');
			l_s+='<tr><td>'+$p.img('ico_chat.gif',16,16,'','imgmid')+'</td><td>'+l_date+'</td><td>'+l_username+'</td><td><a href="#" onclick=\'$p.chat.getArchiveDetail('+$p.ajax.getVal(l_result[i],'id','int',false,0)+',"'+(l_username+' ('+l_date+')')+'")\'>'+$p.ajax.getVal(l_result[i],'title','str',false,'???')+'</a></td></tr>';
		}
		l_s+='</table></td><td valign="top" id="chatdetail" style="border: 1px solid #c6c3c6;background: #efefef;"></div></td></tr></table>';
		if ($p.app.env=='network') $p.print('network_content',l_s)
	},
	getArchiveDetail:function(v_id,v_title)
	{
		$p.ajax.call(pep["xmlchat_archivedetail"]+'?id='+v_id,
			{
				'type':'load',
				'callback':
				{
					'function':$p.chat.displayArchiveDetail,
					'variables':
					{
						'title':v_title
					}
				}
			}
		);
	},
	displayArchiveDetail:function(response,vars)
	{
		var l_s='<b>'+vars['title']+'</b><br /><br />',l_result=response.getElementsByTagName('item');
		for (var i=0;i<l_result.length;i++)
		{
			l_s+='<b>'+$p.ajax.getVal(l_result[i],'username','str',false,'')+'</b> : '+$p.ajax.getVal(l_result[i],'message','str',false,'')+'<br />';
		}
		$p.print('chatdetail',l_s);
	}
}
$p.chat.discussion={
	opened:[],
	tread:function(id,userid,username,status)
	{
		this.id=id;
		this.userid=userid;
		this.username=username;
		this.status=status;
		this.lastSenderId=0;
		this.myStatus='none';
		this.contactStatus='none';
		this.inactivityStep=0;
	},
	/*
		$p.chat.discussion.open: open a chat discussion with a user
		inputs :
			v_userid : id of the user to chat with
	*/
	open:function(v_chatid,v_userid,v_username)
	{
		//Check if a discussion is already existing with the user
		for (var i=0;i<$p.chat.discussion.opened.length;i++)
		{
			if ($p.chat.discussion.opened[i].userid==v_userid)
			{
				$p.chat.discussion.show(i);
				return;
			}
		}
		l_id=($p.chat.discussion.opened.push(new $p.chat.discussion.tread(v_chatid,v_userid,v_username,(v_chatid==0?'N':'O')))-1);
		$p.chat.discussion.addBox(l_id,v_username);
	},
	/*
		$p.chat.discussion.addBox : add a discussion box
		inputs :
			v_id : chat id
			v_title : chat box title
	*/
	addBox:function(v_id,v_title)
	{
		$p.chat.displayArea();

		var chatTitle=new Element('div',
			{
				'class':'title',
				'styles':{
					'width':'298px',
					'height':'24px',
					'font-size':'13px',
					'font-weight':'bold'
				}
			}
		);
		chatTitle.set('html','<div style="float: left;"> '+$p.img('ico_chat.gif',16,16,'','imgmid','chatstatus'+v_id)+' '+v_title+'</div><div style="padding: 5px;float: right;"><a href="#" onclick="$p.chat.discussion.hide('+v_id+');return false;"><img src="../images/ico_close.gif"></a></div>');
		var chatContent=new Element('div',
			{
				'class':'content',
				'id':'chatcontent'+v_id,
				'styles':{
					'width':'100%',
					'height':'150px',
					'overflow':'auto'
				}
			}
		);
		var chatInput=new Element('textarea',
			{
				'name':'chatInput',
				'class':'input',
				'rows':'2',
				'styles':{
					'margin':'2px',
					'width':'292px'
				}
			}
		);
		chatInput.formId=v_id;
		chatInput.set('html',lg('typeYourMessageHere'));
		chatInput.addEvent('keypress',function(event)
			{
				if (event.key=='enter')
				{
					$p.chat.discussion.send(this.formId,this.value);
				}
				else
				{
					$p.chat.discussion.typing(this.formId);
				}
			}
		)
		var chatForm=new Element('form',
			{
				'name':'chatform'+v_id,
				'id':'chatform'+v_id,
				'action':'#',
				'events':{
					'submit':function()
					{
						return false;
					}
				}
			}
		);
		chatInput.injectInside(chatForm);
		var chatDiv=new Element('div',
			{
				'class':'container',
				'id':'chat'+v_id,
				'styles':{
					'width':'300px'
				}
			}
		);
		chatTitle.injectInside(chatDiv);
		chatContent.injectInside(chatDiv);
		chatForm.injectInside(chatDiv);
		chatDiv.injectInside($('chatarea'));
	},
	/*
		$p.chat.discussion.send : send a message
		inputs :
			v_id : chat ID
			v_message : text message to send
	*/
	send:function(v_id,v_message)
	{
		v_message=removeTags(v_message);
		$p.ajax.call(pep["scr_chat_message"],
			{
				'type':'execute',
				'variables':'id='+$p.chat.discussion.opened[v_id].id+'&m='+v_message+'&s='+$p.chat.discussion.opened[v_id].status+($p.chat.discussion.opened[v_id].status=='N'?'&t='+v_message.substr(0,32):'')+'&fid='+$p.chat.discussion.opened[v_id].userid,
				'alarm':false,
				'forceExecution':true,
				'callback':
				{
					'function':$p.chat.discussion.sendAfter,
					'variables':
					{
						'id':v_id
					}
				}
			}
		);

		$p.chat.discussion.write($p.chat.discussion.opened[v_id].id,v_message,$p.app.user.id);
		$p.chat.discussion.opened[v_id].myStatus=='none';
	},
	sendAfter:function(v_chatid,vars)
	{
		$p.chat.discussion.opened[vars['id']].id=v_chatid;
		$p.chat.discussion.opened[vars['id']].status='O';
		document.forms['chatform'+vars['id']].chatInput.value='';
	},
	typing:function(v_chatid)
	{
		$p.chat.discussion.opened[v_chatid].inactivityStep=0;
	},
	/*
		$p.chat.discussion.write : write message on chat
		inputs :
			v_id : chat id
			v_message : message
			v_received = true if the message is received
	*/
	write:function(v_id,v_message,v_sender)
	{
		for (var i=0;i<$p.chat.discussion.opened.length;i++)
		{
			if ($p.chat.discussion.opened[i].id==v_id)
			{
				if (v_sender==indef) v_sender=$p.chat.discussion.opened[i].userid;

				if ($p.chat.discussion.opened[i].lastSenderId!=v_sender)
					$p.print('chatcontent'+i,'<b>'+(v_sender==$p.app.user.id?lg('me'):$p.chat.discussion.opened[i].username)+'</b><br />','bottom');
				$p.print('chatcontent'+i,v_message+'<br />','bottom');
				$p.chat.discussion.opened[i].lastSenderId=v_sender;

				$p.chat.discussion.scroll(i);
			}
		}
		navShow('chat'+v_id,'inline');
		if ($p.chat.minimized) $p.chat.discussion.minimize();//maximize chat windows if minimized
	},
	scroll:function(v_id)
	{
		$('chatcontent'+v_id).scrollTop=$('chatcontent'+v_id).scrollHeight;
	},
	show:function(v_id)
	{
		navShow('chat'+v_id,'inline');
	},
	hide:function(v_id)
	{
		navShow('chat'+v_id,'none');
	},
	/*
		$p.chat.discussion.minimize : minimize all chat windhow
	*/
	minimize:function()
	{
		var l_status=($p.chat.minimized?'block':'none');

		for (var i=0;i<$p.chat.discussion.opened.length;i++)
		{
			if ($('chatcontent'+i)!=null) $('chatcontent'+i).style.display=l_status;
			if ($('chatform'+i)!=null) $('chatform'+i).style.display=l_status;
		}
		$p.chat.minimized=($p.chat.minimized?false:true);
	},
	/*
		$p.chat.discussion.status : change chat status
		inputs
			v_id : ID of the chat
			v_status : new status
	*/
	status:function(v_id,v_status)
	{
		if (v_id==indef) return;
		if ($('chatstatus'+v_id)!=null) $('chatstatus'+v_id).src='../images/ico_chat'+_lc(v_status)+'.gif';
		$p.chat.discussion.opened[v_id].contactStatus=v_status;
	},
	/*
		$p.chat.discussion.getId : get chat ID from user id
	*/
	getId:function(v_userId)
	{
		for (var i=0;i<$p.chat.discussion.opened.length;i++)
		{
			if ($p.chat.discussion.opened[i].userid==v_userId)
				return i;
		}
		return false;
	}
}
$p.network.alert={
    /*
		Function: summary
                                $p.network.alert.summary
                                
                                Initialize alerts area
	*/
    summary: function(v_div)
    {
        var l_s = $p.html.buildTitle(lg('myAlerts'))
            + '<div id="alertssummary"></div>';
        navPrint(v_div,l_s);

        $p.network.alert.get(0);
    },
    /*
		Function: get
                                $p.network.alert.get
                                
                                load alerts
                      Parameters :
                            v_page : alerts list page
	*/
    get: function(v_page)
    {
        var l_modifiedPages = $p.app.pages.getModifiedPages();

        var l_s = '<div id="alertsummary_pages">';

        for (var i = 0;i < l_modifiedPages.length;i++)
        {
            var l_title = (l_modifiedPages[i].status == 1 ? lg('tabAdded',l_modifiedPages[i].title)
                                                          : lg('tabModified',l_modifiedPages[i].title)
            );
            
            l_s += $p.network.alert.item(
                'ico_portal.gif',
                l_title,
                '$p.app.pages.change('+l_modifiedPages[i].id+')'
            );
        }
        l_s += '</div>'
            + '<div id="alertssummary_list"></div>';

        $p.print('alertssummary',l_s);
        
        $p.app.wait('alertssummary_list');
        
        $p.ajax.call(pep["xmlnetwork_alerts"]+'?page='+v_page,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.network.alert.display,
                    'variables':
                    {
                        'page':v_page
                    }
                }
            }
        );
    },
    display: function(response,vars)
    {
        var l_newGroups = response.getElementsByTagName("group"),
            l_s = '';

        for (var i = 0;i < l_newGroups.length;i++)
        {
            l_s += $p.network.alert.item(
                'ico_groups.gif',
                lg('youAreInvitedInGroup',$p.ajax.getVal(l_newGroups[i],'name','str',false,'...')),
                '$p.group.buildPage()'
            );
        }
        
        var l_alerts = response.getElementsByTagName("alert"); 
        
        for (var i = 0;i < l_alerts.length;i++)
        {
            var l_icon = 'ico_alert.gif', l_title, l_fct,
                l_type = $p.ajax.getVal(l_alerts[i],'type','int',false,0);

            switch (l_type)
            {
                case 1:
                    l_icon = 'ico_friend_add.gif';
                    l_title = lg('networkAlert'+l_type,$p.ajax.getVal(l_alerts[i],'refname','str',false,'...'));
                    l_fct = '$p.notebook.open('+$p.ajax.getVal(l_alerts[i],'refid','int',false,0)+',"note","'+$p.ajax.getVal(l_alerts[i],'refname','str',false,'...')+'")';
                    break;
                case 2:
                    l_icon = 'ico_comment.gif';
                    l_title = lg('networkAlert'+l_type,$p.ajax.getVal(l_alerts[i],'refname','str',false,'...'));
                    l_fct = '$p.notebook.open(indef,"note","'+$p.ajax.getVal(l_alerts[i],'refname','str',false,'...')+'",indef,'+$p.ajax.getVal(l_alerts[i],'refid','int',false,0)+')';
                    break;
            }
            l_s += $p.network.alert.item(
                l_icon,
                l_title,
                l_fct
            );
        }
        if (vars['page'] > 0 || l_alerts.length > 10)
        {
            l_s += '<div style="text-align: right">';
            if (vars['page'] > 0)
            {
                l_s += $p.app.tools.buildPreviousLinkIcon('$p.network.alert.get('+(vars['page']-1)+')');
            }
            l_s += " &nbsp; ";
            if  (l_alerts.length > 10)
            {
                l_s += $p.app.tools.buildNextLinkIcon('$p.network.alert.get('+(vars['page']+1)+')');
            }
        }

        if (l_s == '' && $('alertsummary_pages').innerHTML == '')
        {
            l_s += lg('noAlert');
        }

        $p.print('alertssummary_list',l_s);
    },
    /*
		Function: item
                                $p.network.alert.item
                                
                                build an alert item
                      Parameters :
                            v_icon : icon displayed
                            v_title : title displayed
                            v_fct : function called when alert is clicked
	*/
    item: function(v_icon,v_title,v_fct)
    {
        return '<div class="homeitem">'
            + '<a href="#" onclick=\''+v_fct+'\'>'
            + $p.img(v_icon,16,16,indef,'imgmid')
            + ' '+ v_title
            + '</a>'
            + '</div>';
    }
}
