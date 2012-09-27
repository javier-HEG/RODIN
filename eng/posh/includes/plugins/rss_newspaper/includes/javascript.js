
var rssNewspaperFolder="../includes/plugins/rss_newspaper/";
var rssNewspaper={
	menu:new Array({"id":1,"title":"rssNewspaper","icon":rssNewspaperFolder+"images/menu_icon.gif","fct":"","isLink":false},{"id":2,"title":"newspapers","icon":"","fct":"rssNewspaper.loadDashboard()","isLink":true},{"id":3,"title":"newNewspaper","icon":rssNewspaperFolder+"images/ico_add.gif","fct":"rssNewspaper.addForm(0,'','','','','')","isLink":true}),
	open:function(){
		$p.plugin.open();
		$p.plugin.init(lg("myNewspapers"));
		rssNewspaper.loadDashboard();
	},
	loadDashboard:function()
	{
		$p.plugin.menu(rssNewspaper.menu,2);

		var l_s="<form onsubmit='return rssNewspaper.search(this)'><b>"+lg("searchNewspaper")+"</b> : <input type='text' id='searchinput' name='searchtxt' size='50' maxlength='100' onkeyup=\"$p.tags.autocompletion.get('searchinput')\" onblur='$p.tags.autocompletion.hide()' /> <input type='submit' value='"+lg("search")+"' /></form><div id='np_searchresults'></div><br />";
		l_s+="<br /><div class='subtitle'>"+lg("lastPublications")+"</div><br /><div id='publicationsdiv'></div>";
		l_s+="<br /><div class='subtitle'>"+lg("myPublications")+"</div><br /><div id='mypublicationsdiv'></div>";
		l_s+="<br /><div class='subtitle'>"+lg("myNewspapers")+"</div><br /><a href='#' onclick=\"rssNewspaper.addForm(0,'','','','','');\"><img src='"+rssNewspaperFolder+"images/ico_add.gif' /> "+lg("newNewspaper")+"</a><br /><br /><div id='mynewspapersdiv'></div>";

		$p.plugin.content(l_s);

		$p.app.wait("publicationsdiv");
		$p.app.wait("mypublicationsdiv");
		$p.app.wait("mynewspapersdiv");

		getXml(rssNewspaperFolder+"scripts/xmldashboard.php",rssNewspaper.displayDashboard);
	},
	displayDashboard:function(response,vars)
	{
		var l_s="",l_id;
		
		var l_result=response.getElementsByTagName("publication");

		if (l_result.length>0)
		{
			p_table.reset();
			p_table.name="publicationtable";
			p_table.container="publicationsdiv";
			p_table.saveincookie=true;
			p_table.width="800px";
			p_table.headers[0]=new p_table.headerObj("title",lg("title"),true,indef,false);
			p_table.init();

			for (var i=0;i<l_result.length;i++)
			{
				l_id=getXMLval(l_result[i],"id","int",false,0);
				l_title=getXMLval(l_result[i],"title","str",false,"---")+" - "+$p.date.formatDateShort($p.date.convertFromDb(getXMLval(l_result[i],"pubdate","str",false,"")))+" &nbsp; ["+getXMLval(l_result[i],"author","str",false,"")+"]";

				p_table.rows[i]=new p_table.row();
				p_table.rows[i].columns[0]=new p_table.cell(l_id,"<a href='../includes/plugins/rss_newspaper/archive/"+getXMLval(l_result[i],"filename","str",false,"")+"' target='_blank'>"+l_title+"</a>");
			}
			p_table.show();
		}
		else
		{
			$p.print("publicationsdiv",lg("noPublication"));
		}

		var l_result=response.getElementsByTagName("mypublication");

		if (l_result.length>0)
		{
			p_table.reset();
			p_table.name="mypublicationtable";
			p_table.container="mypublicationsdiv";
			p_table.saveincookie=true;
			p_table.width="800px";
			p_table.headers[0]=new p_table.headerObj("title",lg("title"),true,"600px",false);
			p_table.headers[1]=new p_table.headerObj("actions","",false);
			p_table.init();

			for (var i=0;i<l_result.length;i++)
			{
				l_id=getXMLval(l_result[i],"id","int",false,0);
				l_title=getXMLval(l_result[i],"title","str",false,"---")+" - "+$p.date.formatDateShort($p.date.convertFromDb(getXMLval(l_result[i],"pubdate","str",false,"")));

				p_table.rows[i]=new p_table.row();
				p_table.rows[i].columns[0]=new p_table.cell(l_title,"<a href='../includes/plugins/rss_newspaper/archive/"+getXMLval(l_result[i],"filename","str",false,"")+"' target='_blank'>"+l_title+"</a>");
				p_table.rows[i].columns[1]=new p_table.cell("","<a href='#' onclick='rssNewspaper.removePublication("+l_id+")'>"+lg("lblSuppress")+"</a>");
			}
			p_table.show();
		}
		else
		{
			$p.print("mypublicationsdiv",lg("noPublication"));
		}

		var l_result=response.getElementsByTagName("mynewspaper");

		if (l_result.length>0)
		{
			p_table.reset();
			p_table.name="mynewspapertable";
			p_table.container="mynewspapersdiv";
			p_table.saveincookie=true;
			p_table.width="800px";
			p_table.headers[0]=new p_table.headerObj("title",lg("title"),true,"600px",false);
			p_table.headers[1]=new p_table.headerObj("actions","",false);
			p_table.init();

			for (var i=0;i<l_result.length;i++)
			{
				l_id=getXMLval(l_result[i],"id","int",false,0);
				l_title=getXMLval(l_result[i],"title","str",false,"---");

				p_table.rows[i]=new p_table.row();
				p_table.rows[i].columns[0]=new p_table.cell(l_title,"<a href='#' onclick='rssNewspaper.getInfo("+l_id+")'>"+l_title+"</a>");
				p_table.rows[i].columns[1]=new p_table.cell("","<a href='#' onclick='rssNewspaper.getInfo("+l_id+")'>"+lg("modify")+"</a> <a href='#' onclick='rssNewspaper.publish.init("+l_id+");'>"+lg("publish")+"</a> <a href='#' onclick='rssNewspaper.removeNewspaper("+l_id+");return false;'>"+lg("lblSuppress")+"</a>");
			}
			p_table.show();
		}
		else
		{
			$p.print("mynewspapersdiv",lg("noNewspaper"));
		}
	},
	getInfo:function(v_id)
	{
		getXml(rssNewspaperFolder+"scripts/xmlnewspaper.php?id="+v_id,rssNewspaper.fillAddForm);
	},
	fillAddForm:function(response,vars)
	{
		var l_selectedFeeds=[];
		var l_feeds=response.getElementsByTagName("feed");
		for (var i=0;i<l_feeds.length;i++)
		{
			l_selectedFeeds.push(getXMLval(l_feeds[i],"fid","int",false,0));
		}

		var l_selectedTags=[];
		var l_tags=response.getElementsByTagName("tag");
		for (var i=0;i<l_tags.length;i++)
		{
			l_selectedTags.push(getXMLval(l_tags[i],"label","str",false,""));
		}
		var l_tag=l_selectedTags.join(",");

		rssNewspaper.addForm(getXMLval(response,"id","int",false,0),getXMLval(response,"title","str",false,""),getXMLval(response,"description","str",false,""),l_tag,getXMLval(response,"header","str",false,""),l_selectedFeeds);
	},
	addForm:function(v_id,v_title,v_description,v_tags,v_headerImg,v_selectedFeeds)
	{
		if (v_id==0)
		{
			$p.plugin.menu(rssNewspaper.menu,3);
		}
		else
		{
			$p.plugin.menu(rssNewspaper.menu,0);
		}

		var l_s="";
		if (v_id==0) l_s+="<div class='subtitle'>"+lg("newNewspaper")+"</div>";

		l_s+="<form name='newspaperAddForm' onsubmit='return rssNewspaper.addNewspaper(this)'>";
		l_s+="<input type='hidden' name='id' value='"+v_id+"' /><input id='rssnpheaderinput' name='header' type='hidden' value='"+v_headerImg+"' />";
		l_s+="<table>";
		l_s+="<tr><td>"+lg("title")+"</td><td><input name='title' class='thinbox' type='text' size='40' maxlength='60' value=\""+v_title+"\"></td></tr>";
		l_s+="<tr><td>"+lg("description")+"</td><td><textarea name='description' cols='40' rows='3'>"+v_description+"</textarea></td></tr>";
		l_s+="<tr><td>"+lg("tags")+"</td><td><input name='keywords' class='thinbox' type='text' size='40' maxlength='150' onkeyup=\"$p.tags.autocompletion.get('keywords')\" onblur='$p.tags.autocompletion.hide()' value=\""+v_tags+"\"></td></tr>";
		l_s+="<tr><td>"+lg("headerImg")+"</td><td><div id='headerimgdiv' style='font-size: 50px;font-family: times;border: 1px solid #000000;width:562px;text-align: center;'>";
		if (v_id!=0)
		{
			if (v_headerImg!="")
			{
				l_s+="<img src='"+rssNewspaperFolder+"upload/"+v_headerImg+"' />";
			}
			else
			{
				l_s+=v_title;
			}
			l_s+='<br /><a href="#" onclick="rssNewspaper.askHeader()" style="font-size: 8pt;">'+lg('lblModify')+'</a>';
		}
		l_s+="</div></td></tr>";
		
		l_s+="<tr><td></td><td>";
		l_s+="<table><tr>";
		l_s+="<td><b>"+lg("selectedFeeds")+"</b><br /><div id='feedlist' style='padding: 8px;width: 400px;height: 200px;border: 1px solid #000000;overflow: auto;'></div></td>";
		l_s+="<td><b>"+lg("myFeeds")+"</b><br /><div id='myFeedsForNewspaper' style='padding: 8px;width: 400px;height: 200px;border: 1px solid #000000;overflow: auto;'></div></td>";
		l_s+="</tr></table>";
		l_s+="</td></tr>";
		l_s+="<tr><td></td><td><input type='submit' class='btn' value='"+lg("lblBtnValid")+"' /> <a href='#' onclick='rssNewspaper.loadDashboard();'>"+lg("cancel")+"</a></td></tr>";
		l_s+="</table>";

		$p.plugin.content(l_s);

		if (v_id==0) rssNewspaper.askHeader();

		rssNewspaper.loadMyFeeds(v_selectedFeeds);
	},
	askHeader:function()
	{
		$p.print('headerimgdiv',"<table><tr><td><iframe id='frmrssnpheader' frameborder=0 src='../includes/plugins/rss_newspaper/scripts/upload_header.php' width='390' height='75'></iframe></td><td valign='top' style='padding-top: 15px;font-size: 10pt;'>"+lg("headerRestriction")+"</td></tr></table>");
	},
	uploadHeader:function(headername)
	{
		document.forms["newspaperAddForm"].elements["header"].value=headername;
		$p.print("headerimgdiv",'<img src="'+rssNewspaperFolder+'upload/'+headername+'" /><br /><a href="#" onclick="rssNewspaper.askHeader()" style="font-size: 8pt;">'+lg('lblModify')+'</a>');
	},
	feeds:[],
	feedObj:function(id,title,selected)
	{
		this.id=id;
		this.title=title;
		this.selected=selected;
	},
	loadMyFeeds:function(v_selectedFeeds)
	{
		getXml(rssNewspaperFolder+"scripts/xmlmyfeeds.php",rssNewspaper.recordMyFeeds,v_selectedFeeds);
	},
	recordMyFeeds:function(response,vars)
	{
		var l_s="",l_result=response.getElementsByTagName("widget");

		for (var i=0;i<l_result.length;i++)
		{
			var l_var=getXMLval(l_result[i],"variables","var",false,"");
			var l_title=getVar(l_var,"ptitl");
			var l_feedid=getVar(l_var,"pfid");
			//default title if not set in the widget parameters
			if (l_title=="") l_title=getXMLval(l_result[i],"title","str",false,"no title");
			
			//if selected feeds are loaded 
			if (vars.length==0)
			{
				var l_selected=0;
			}
			else
			{
				if (inArray(vars,l_feedid))
				{
					var l_selected=1;
				}
				else
				{
					var l_selected=0;
				}
			}
			rssNewspaper.feeds[i]=new rssNewspaper.feedObj(l_feedid,l_title,l_selected);
		}

		rssNewspaper.feeds.sort(rssNewspaper.sortFeeds);
		rssNewspaper.displayFeeds();
	},
	sortFeeds:function(a,b)
	{
		if (_lc(a.title)>_lc(b.title)) return 1;
		if (_lc(a.title)<_lc(b.title)) return -1;
		return 0;
	},
	displayFeeds:function()
	{
		var l_feedsList="",l_availableFeedsList="";
		for (var i=0;i<rssNewspaper.feeds.length;i++)
		{
			if (rssNewspaper.feeds[i].selected==0)
			{
				l_availableFeedsList+="<a href='#' onclick='rssNewspaper.selectFeed("+i+");return false;'>"+rssNewspaper.feeds[i].title+"<br />";
			}
			else
			{
				l_feedsList+=rssNewspaper.feeds[i].title+" [<a href='#' onclick='rssNewspaper.removeFeed("+i+");return false;'>x</a>]<br />";
			}
		}
		if (l_availableFeedsList=="" && l_feedsList=="") l_availableFeedsList=lg("pleaseAddFeedsInPages");

		$p.print("myFeedsForNewspaper",l_availableFeedsList);
		$p.print("feedlist",l_feedsList);
	},
	selectFeed:function(v_i)
	{
		rssNewspaper.feeds[v_i].selected=1;
		rssNewspaper.displayFeeds();
	},
	removeFeed:function(v_i)
	{
		rssNewspaper.feeds[v_i].selected=0;
		rssNewspaper.displayFeeds();
	},
	addNewspaper:function(v_form)
	{
		//feeds selection
		var l_selFeeds="",l_inc=0;
		for (var i=0;i<rssNewspaper.feeds.length;i++)
		{
			if (rssNewspaper.feeds[i].selected==1)
			{
				l_selFeeds+="&f"+l_inc+"="+rssNewspaper.feeds[i].id;
				l_inc++;
			}
		}
		//tags format
		var l_keywords=$p.tags.formatList(v_form.keywords.value);
		l_kwformated=formatSearch(l_keywords);

		executescr(rssNewspaperFolder+"scripts/scr_add_newspaper.php","id="+v_form.id.value+"&t="+$p.string.esc(v_form.title.value)+"&d="+$p.string.esc(v_form.description.value)+l_selFeeds+"&h="+v_form.header.value+"&kw="+l_keywords+"&kwformated="+l_kwformated,true,false,rssNewspaper.addSuccess);

		return false;
	},
	addSuccess:function()
	{
		rssNewspaper.loadDashboard();
	},
	removeNewspaper:function(v_id)
	{
		var response=confirm(lg("msgAreYouSureSupElement"));
		if (response==1)
		{
			executescr(rssNewspaperFolder+"scripts/scr_remove_newspaper.php","id="+v_id,true,false,rssNewspaper.loadDashboard);
		}
	},
	removePublication:function(v_id)
	{
		var response=confirm(lg("msgAreYouSureSupElement"));
		if (response==1)
		{
			executescr(rssNewspaperFolder+"scripts/scr_remove_publication.php","id="+v_id,true,false,rssNewspaper.loadDashboard);
		}
	},
	search:function(v_form)
	{
		$p.app.wait("np_searchresults");
		var l_s=formatSearch(v_form.searchtxt.value);
		getXml(rssNewspaperFolder+"scripts/xmlsearch.php?searchtxt="+$p.string.esc(l_s)+"&p=0",rssNewspaper.displaySearchResults);
		return false;
	},
	displaySearchResults:function(response,vars)
	{
		var l_result=response.getElementsByTagName("result"),l_s="";
		if (l_result.length==0)
		{
			l_s+=lg("noResultForThisSearch");
		}
		else
		{
			for (var i=0;i<l_result.length;i++)
			{
				var l_title=getXMLval(l_result[i],"title","str",false,"no title")+" - "+$p.date.formatDateShort($p.date.convertFromDb(getXMLval(l_result[i],"pubdate","str",false,"")));

				l_s+="<br /><a href='../includes/plugins/rss_newspaper/archive/"+getXMLval(l_result[i],"filename","str",false,"")+"' target='_blank'>"+getXMLval(l_result[i],"title","str",false,"no title")+" - "+getXMLval(l_result[i],"pubdate","str",false,"")+"</a>";
			}
		}
		$p.print("np_searchresults",l_s);
	}
}
rssNewspaper.publish={
	id:0,
	article:[],
	pageLayout:[],
	layout:[],
	defaultLayout:5,
	pageWidth:844,
	pageHeight:1160,
	pageNb:1,
	header:"",
	title:"",
	topMargin:0,
	leftMargin:0,
	articleObj:function(feed,title,link,body,image,pubdate)
	{
		this.feed=feed;
		this.title=title;
		this.link=link;
		this.body=body;
		this.image=image;
		this.pubdate=pubdate;
		this.page=0;
		this.top=0;
		this.left=0;
		this.width=0;
		this.height=0;
	},
	init:function(v_id)
	{
		rssNewspaper.publish.id=v_id;
		$p.plugin.menu(rssNewspaper.menu,0);

		$p.plugin.wait();

		rssNewspaper.publish.article.length=0;
		rssNewspaper.publish.pageLayout.length=0;
		rssNewspaper.publish.pageNb=1;

		rssNewspaper.publish.load(); 
	},
	load:function()
	{
		getXml(rssNewspaperFolder+"scripts/xmlarticlespublished.php?id="+rssNewspaper.publish.id,rssNewspaper.publish.register)
	},
	clean:function(v_s)
	{
		v_s=$p.string.htmlToText(v_s);
		v_s=$p.string.removeTags(v_s);
		v_s=v_s.replace(/\n/g,"<br />");
		while (v_s.indexOf("<br /><br />")!=-1)
		{
			v_s=v_s.replace(/<br \/><br \/>/g,"<br />");
		}
		if (v_s.indexOf("<br")==0)
		{
			v_s=v_s.replace(/<br \/>/,"");
		}
		return v_s;
	},
	register:function(response,vars)
	{
		rssNewspaper.publish.header=getXMLval(response,"header","str",false,"");
		rssNewspaper.publish.title=getXMLval(response,"title","str",false,"");

		var l_result=response.getElementsByTagName("article"),l_date;

		if (l_result.length==0)
		{
			alert(lg("noNewArticleForThisNewspaper"));
			rssNewspaper.loadDashboard();
		}
		else
		{
			//other articles read from DB
			for (var i=0;i<l_result.length;i++)
			{
				l_date=$p.date.convertFromRss(getXMLval(l_result[i],"pubdate","str",false,""));
				l_body=rssNewspaper.publish.clean(getXMLval(l_result[i],"desc","str",false,""));
				rssNewspaper.publish.article[i]=new rssNewspaper.publish.articleObj(getXMLval(l_result[i],"feed","str",false,"no title"),getXMLval(l_result[i],"title","str",false,"no title"),getXMLval(l_result[i],"link","str",false,""),l_body,getXMLval(l_result[i],"image","str",false,""),l_date);
				
				//no gif in the images
				if ((rssNewspaper.publish.article[i].image).indexOf('.gif')!=-1 || (rssNewspaper.publish.article[i].image).indexOf('.GIF')!=-1)
				{
					rssNewspaper.publish.article[i].image='';
				}
			}

			rssNewspaper.publish.article.sort(rssNewspaper.publish.sortByDate)
			rssNewspaper.publish.build();
		}
	},
	/*
		sort articles by date
	*/
	sortByDate:function(a,b)
	{
		if (a.pubdate>b.pubdate) return -1;
		if (a.pubdate<b.pubdate) return 1;
		return 0;
	},
	/*
		build an article element
	*/
	buildElement:function(v_id)
	{
		var l_bigSize=(v_id==0?"20pt":"16pt");
		var l_middleSize=(v_id==0?"14pt":"16px");
		var l_s="";
		l_s+="<div id='rssnp_arttitle"+v_id+"' style='font-size:"+l_bigSize+";font-family:times;'>"+rssNewspaper.publish.article[v_id].title+"</div><br />";
		l_s+="<div style='padding-bottom: 5px;font-family:times;font-size:"+l_middleSize+";'>"+rssNewspaper.publish.article[v_id].feed+" | "+$p.date.formatDateLong(rssNewspaper.publish.article[v_id].pubdate)+"</div><br />";
		l_s+="<img id='rssnp_artimage"+v_id+"' src='"+(rssNewspaper.publish.article[v_id].image==""?"../images/s.gif":rssNewspaper.publish.article[v_id].image)+"' style='margin-bottom: 5px;' />";
		l_s+="<div id='rssnp_artbody"+v_id+"' style='text-align: justify;font-size:"+l_middleSize+";font-family:times;'>"+rssNewspaper.publish.article[v_id].body+"</div>";
		l_s+="<div id='rssnp_artoptions"+v_id+"' style='width: 100%;background: #efefef;border: 1px solid #c6c3c6;border-bottom: 1px solid #000000;height: 20px;font-size: 8pt;padding: 1px'><a href='#' onclick='rssNewspaper.publish.removeArticle("+v_id+");return false;'>"+lg("lblSuppress")+"</a> | <a href='#' onclick='rssNewspaper.publish.moveArticle("+v_id+");return false;'>"+lg("moveup")+"</a> | <a href='#' onclick='rssNewspaper.publish.modifyArticle("+v_id+");'>"+lg("modify")+"</a> | <a href='#' onclick='rssNewspaper.publish.addImage("+v_id+");'>"+lg("image")+"</a></div>";
		//l_s+="</div>";
		return l_s;
	},
	/*
		build page without articleplacement
	*/
	build:function()
	{
		$p.app.popup.show(lg("buildingNewspaper")+" ...",400);

		var l_s="";
		l_s+="<div id='rssnpnavigator' style='height: 30px;width: "+rssNewspaper.publish.pageWidth+"px;background: #efefef;border: 1px solid #c6c3c6;padding: 5px;font-size: 12pt;overflow: hidden;'></div><br />";
		l_s+="<div id='rssnp_page' style='width: "+rssNewspaper.publish.pageWidth+"px;height: "+rssNewspaper.publish.pageHeight+"px;border: 2px solid #c6c3c6;text-align: left;'>"
		l_s+=(rssNewspaper.publish.header==""?"<div id='rssnpheader' style='font-size: 75px;text-align: center;width: 100%;font-family: times;border-bottom: 2px solid #000000;'>"+rssNewspaper.publish.title+"</div>":"<img id='rssnpheader' src='../includes/plugins/rss_newspaper/upload/"+rssNewspaper.publish.header+"' />");
		l_s+="<div id='rssnpdatearea' style='text-align: center;height: 24px;font-size: 17pt;font-family: times;border-bottom: 1px solid #000000;'>"+rssNewspaper.publish.title+"</div>";
		for (var i=0;i<rssNewspaper.publish.article.length;i++)
		{
			l_s+="<div id='rssnp_art"+i+"' style='position: absolute;top: 150px;left: 5px;'>";
			l_s+=rssNewspaper.publish.buildElement(i);
			l_s+="</div>";
		}
		l_s+="</div>";
		l_s+="<div id='rssnp_index' style='position: absolute;width: 257px;border-right: 1px solid #000000;text-align: justify;font-size:10pt;font-family:times;padding: 8px;'></div>";
		$p.plugin.content(l_s);

		//wait 10 seconds for image loading
		setTimeout("rssNewspaper.publish.placeArticles()",10000);
	},
	//place articles
	placeArticles:function(v_page)
	{
		if (v_page==indef) v_page=1;

		//intro page layout
		rssNewspaper.publish.layout[0]=new Array({"left":286,"width":271},{"left":567,"width":271});
		//layout for other pages
		rssNewspaper.publish.layout[1]=new Array({"left":5,"width":834});
		rssNewspaper.publish.layout[2]=new Array({"left":5,"width":412},{"left":427,"width":412});
		rssNewspaper.publish.layout[3]=new Array({"left":5,"width":562},{"left":577,"width":262});
		rssNewspaper.publish.layout[4]=new Array({"left":5,"width":262},{"left":277,"width":562});
		rssNewspaper.publish.layout[5]=new Array({"left":5,"width":271},{"left":286,"width":271},{"left":567,"width":271});
		rssNewspaper.publish.layout[6]=new Array({"left":5,"width":201},{"left":216,"width":201},{"left":427,"width":201},{"left":638,"width":201});

		var l_inc=0;
		var l_page=v_page;
	
		var l_headerHeight=($("rssnpheader")).offsetHeight;
		
		if (rssNewspaper.publish.pageLayout.length==0)
		{
			rssNewspaper.publish.pageLayout[1]=rssNewspaper.publish.defaultLayout;
			if (rssNewspaper.publish.header!="")
			{
				//change image scale to align with PDF size
				l_headerHeight=l_headerHeight*1.5;
				($("rssnpheader")).style.height=l_headerHeight+"px";
			}
		}
		// add 20px height for the date
		l_headerHeight+=40;
		
		//set the top of each column	
		rssNewspaper.publish.topMargin=getPos($("rssnp_page"),"Top")+6;
		var l_topAreaWithHeader=rssNewspaper.publish.topMargin+l_headerHeight;
		var l_availHeight=rssNewspaper.publish.pageHeight-12;
		var l_availHeightWithHeader=l_availHeight-l_headerHeight;
		rssNewspaper.publish.leftMargin=getPos($("rssnp_page"),"Left");
		var l_currTopArea=l_topAreaWithHeader;
		var l_currAvailHeight=l_availHeightWithHeader;

		var l_topPos=[];
		rssNewspaper.publish.pageLayout[1]=0; //layout 3 columns for page 1
		for (var i=0;i<rssNewspaper.publish.layout[rssNewspaper.publish.pageLayout[l_page]].length;i++)
		{
			l_topPos[i]=l_currTopArea;
		}

		//place the articles
		for (var i=0;i<rssNewspaper.publish.article.length;i++)
		{
			if (rssNewspaper.publish.article[i].page==0 || rssNewspaper.publish.article[i].page>=v_page)
			{
				//find the first available place
				l_inc=0,l_minTop=l_topPos[0];
				for (var j=0;j<rssNewspaper.publish.layout[rssNewspaper.publish.pageLayout[l_page]].length;j++)
				{
					if (l_topPos[j]<l_minTop)
					{
						l_inc=j;
						l_minTop=l_topPos[j];
					}
				}

				//place the article
				$p.show("rssnp_art"+i,"block"); // needs to be displayed to compute height
				var l_divArticle=$("rssnp_art"+i);
				var l_divArticleHeight=l_divArticle.offsetHeight;

				if (i==0)
				{
					$('rssnp_index').style.top=l_topPos[l_inc]+"px";
					$('rssnp_index').style.left=(rssNewspaper.publish.leftMargin+5)+"px";
				}

				rssNewspaper.publish.article[i].top=l_topPos[l_inc];
				l_divArticle.style.top=rssNewspaper.publish.article[i].top+"px";

				rssNewspaper.publish.article[i].left=rssNewspaper.publish.layout[rssNewspaper.publish.pageLayout[l_page]][l_inc].left+rssNewspaper.publish.leftMargin;
				l_divArticle.style.left=rssNewspaper.publish.article[i].left+"px";

				rssNewspaper.publish.article[i].width=rssNewspaper.publish.layout[rssNewspaper.publish.pageLayout[l_page]][l_inc].width;
				if (i==0) rssNewspaper.publish.article[i].width=rssNewspaper.publish.article[i].width*2;
				l_divArticle.style.width=rssNewspaper.publish.article[i].width+"px";

				l_divArticleHeight=l_divArticle.offsetHeight;
				rssNewspaper.publish.article[i].height=l_divArticleHeight; // need to be process after div width set
			
				//if article is out the page, change page
				if (l_topPos[l_inc]+l_divArticleHeight>l_currTopArea+l_currAvailHeight)
				{
					if (l_topPos[l_inc]==l_currTopArea)
					{
						while (l_divArticleHeight>l_currAvailHeight && rssNewspaper.publish.article[i].body.length>30)
						{
							rssNewspaper.publish.article[i].body=rssNewspaper.publish.article[i].body.substr(0,rssNewspaper.publish.article[i].body.length-30)+" ...";
							$p.print("rssnp_artbody"+i,rssNewspaper.publish.article[i].body);

							l_divArticleHeight=($("rssnp_art"+i)).offsetHeight;
						}
					}
					else
					{
						//create a new page
						l_page++;
						
						//initialize layout array
						if (rssNewspaper.publish.pageLayout[l_page]==indef)
						{
							rssNewspaper.publish.pageLayout[l_page]=rssNewspaper.publish.defaultLayout;
						}
				
						l_currTopArea=rssNewspaper.publish.topMargin;
						l_currAvailHeight=l_availHeight;
						l_inc=0;
						for (var k=0;k<rssNewspaper.publish.layout[rssNewspaper.publish.pageLayout[l_page]].length;k++)
						{
							l_topPos[k]=l_currTopArea;
						}

						rssNewspaper.publish.article[i].top=l_topPos[l_inc];
						l_divArticle.style.top=rssNewspaper.publish.article[i].top+"px";

						rssNewspaper.publish.article[i].left=rssNewspaper.publish.layout[rssNewspaper.publish.pageLayout[l_page]][l_inc].left+rssNewspaper.publish.leftMargin;
						l_divArticle.style.left=rssNewspaper.publish.article[i].left+"px";
			
						rssNewspaper.publish.article[i].width=rssNewspaper.publish.layout[rssNewspaper.publish.pageLayout[l_page]][l_inc].width;
						l_divArticle.style.width=rssNewspaper.publish.article[i].width+"px";

						rssNewspaper.publish.article[i].height=l_divArticle.offsetHeight;
					}
				}
				rssNewspaper.publish.article[i].page=l_page;

				//get the new available place for this column
				l_topPos[l_inc]=l_topPos[l_inc]+rssNewspaper.publish.article[i].height;
				if (i==0)
				{
					l_topPos[l_inc+1]=l_topPos[l_inc+1]+rssNewspaper.publish.article[i].height;
				}
			}
		}
		rssNewspaper.publish.pageNb=l_page;

		$p.app.popup.hide();

		rssNewspaper.publish.buildIndex();

		rssNewspaper.publish.displayPage(v_page);
	},
	/*
		display navigation bar and articles depending on the opened page
	*/
	displayPage:function(v_pageId)
	{
		var l_s="";
		if (v_pageId>1) l_s+=" &nbsp; <a href='#' onclick='rssNewspaper.publish.displayPage("+(v_pageId-1)+")'><img src='../images/ico_previous2.gif' /></a>";
		l_s+=" <b>"+lg("page")+" "+v_pageId+" / "+rssNewspaper.publish.pageNb+"</b>";
		if (v_pageId<rssNewspaper.publish.pageNb) l_s+=" <a href='#' onclick='rssNewspaper.publish.displayPage("+(v_pageId+1)+")'><img src='../images/ico_next2.gif' /></a>";
		//layout feature only available in page > 1
		if (v_pageId>1)
		{
			l_s+=" &nbsp; "+lg("pageFormat")+" : ";
			for (var i=1;i<rssNewspaper.publish.layout.length;i++)
			{
				l_s+=" <a href='#' "+(i==rssNewspaper.publish.pageLayout[v_pageId]?"style='border:1px solid #ff0000;' ":"")+"onclick='rssNewspaper.publish.changeLayout("+i+","+v_pageId+");return false;'>"+img("../includes/plugins/rss_newspaper/images/layout"+i+".png")+"</a>";
			}
		}
		l_s+=" &nbsp; <input type='button' class='submit' value='"+lg("publish")+"' onclick='rssNewspaper.publish.save()' />";
		$p.print("rssnpnavigator",l_s);

		if (v_pageId==1)
		{
			$p.show("rssnpheader","block");
			$p.show("rssnpdatearea","block");
		}
		else
		{
			$p.show("rssnpheader","none");
			$p.show("rssnpdatearea","none");
		}

		for (var i=0;i<rssNewspaper.publish.article.length;i++)
		{
			if (rssNewspaper.publish.article[i].page==v_pageId)
			{
				$p.show("rssnp_art"+i,"block");
			}
			else
			{
				$p.show("rssnp_art"+i,"none");
			}
		}
		if (v_pageId==1)
		{
			$p.show('rssnp_index','block');
		}
		else
		{
			$p.show('rssnp_index','none');
		}
	},
	/*
		build and show index
	*/
	buildIndex:function()
	{
		var l_s='<b>'+lg('index')+'</b>';
		var l_page=0;
		for (var i=0;i<_min(rssNewspaper.publish.article.length,35);i++)
		{
			if (l_page<rssNewspaper.publish.article[i].page)
			{
				l_s+='<br /><br />Page '+rssNewspaper.publish.article[i].page;
				l_page=rssNewspaper.publish.article[i].page;
			}
			l_s+='<br />'+$p.string.trunk(rssNewspaper.publish.article[i].title,37);
		}
		if (rssNewspaper.publish.article.length>35) l_s+='<br /><br />...';
		$p.print('rssnp_index',l_s);
	},
	/*
		change page layout
	*/
	changeLayout:function(v_layout,v_page)
	{
		rssNewspaper.publish.pageLayout[v_page]=v_layout;
		rssNewspaper.publish.placeArticles(v_page);
	},
	removeArticle:function(v_id)
	{
		var response=confirm(lg("msgAreYouSureSupElement"));
		if (response==1)
		{
			var l_page=rssNewspaper.publish.article[v_id].page;

			rssNewspaper.publish.article.splice(v_id,1);
			($("rssnp_page")).removeChild($("rssnp_art"+v_id));
			//document.body.removeChild($("rssnp_art"+v_id));

			for (var i=v_id;i<rssNewspaper.publish.article.length;i++)
			{
				rssNewspaper.publish.removeContent(i+1);
				rssNewspaper.publish.changeElementId(i+1,i);
				$p.print("rssnp_art"+i,rssNewspaper.publish.buildElement(i));
			}

			rssNewspaper.publish.placeArticles(l_page);
		}
	},
	modifyArticle:function(v_id)
	{
		var l_s="<form onsubmit='return rssNewspaper.publish.saveArticleModifications("+v_id+",this.newarticlebody.value)'>";
		l_s+="<textarea name='newarticlebody' style='width: 470px;height: 280px;'>"+$p.string.htmlToText(rssNewspaper.publish.article[v_id].body)+"</textarea><br />";
		l_s+="<input type='submit' value='"+lg("modify")+"' /> <a href='#' onclick='$p.app.popup.hide()'>"+lg("cancel")+"</a>";
		l_s+="</form>";
		$p.app.popup.show(l_s,500);
	},
	saveArticleModifications:function(v_id,v_newbody)
	{
		rssNewspaper.publish.article[v_id].body=$p.string.textToHtml($p.string.removeTags(v_newbody));
		$p.print("rssnp_artbody"+v_id,rssNewspaper.publish.article[v_id].body);

		rssNewspaper.publish.placeArticles(rssNewspaper.publish.article[v_id].page);

		return true;
	},
	moveArticle:function(v_id)
	{
		if (v_id>0)
		{
			//remove content of article
			rssNewspaper.publish.removeContent(v_id);
			rssNewspaper.publish.removeContent(v_id-1);

			//exchange divs
			rssNewspaper.publish.changeElementId(v_id,"temp");
			rssNewspaper.publish.changeElementId(v_id-1,v_id);
			rssNewspaper.publish.changeElementId("temp",v_id-1);

			//exchange article objects
			var l_tempArticle=new rssNewspaper.publish.articleObj(rssNewspaper.publish.article[v_id].feed,rssNewspaper.publish.article[v_id].title,rssNewspaper.publish.article[v_id].link,rssNewspaper.publish.article[v_id].body,rssNewspaper.publish.article[v_id].image,rssNewspaper.publish.article[v_id].pubdate); 
			rssNewspaper.publish.article[v_id]=rssNewspaper.publish.article[v_id-1];
			rssNewspaper.publish.article[v_id-1]=l_tempArticle;

			//rebuild articles
			$p.print("rssnp_art"+v_id,rssNewspaper.publish.buildElement(v_id));
			$p.print("rssnp_art"+(v_id-1),rssNewspaper.publish.buildElement(v_id-1));

			rssNewspaper.publish.displayPage(rssNewspaper.publish.article[v_id].page);
			rssNewspaper.publish.placeArticles(rssNewspaper.publish.article[v_id].page);
		}
	},
	changeArticleTitleSize:function(v_id,v_size)
	{
		($('rssnp_arttitle'+v_id)).style.fontSize=v_size+'pt';
	},
	changeElementId:function(v_oldId,v_newId)
	{
		navId("rssnp_art"+v_oldId,"rssnp_art"+v_newId);
		//navId("rssnp_artbody"+v_oldId,"rssnp_artbody"+v_newId);
		//navId("rssnp_artoptions"+v_oldId,"rssnp_artoptions"+v_newId);
		//navId("rssnp_artimage"+v_oldId,"rssnp_artimage"+v_newId);
	},
	removeContent:function(v_id)
	{
		($("rssnp_art"+v_id)).removeChild($("rssnp_artbody"+v_id));
		($("rssnp_art"+v_id)).removeChild($("rssnp_artoptions"+v_id));
		($("rssnp_art"+v_id)).removeChild($("rssnp_artimage"+v_id));
	},
	addImage:function(v_id)
	{
		var l_s="<iframe src='../includes/plugins/rss_newspaper/scripts/upload_image.php?id="+v_id+"&maxsize="+parseInt((rssNewspaper.publish.article[v_id].width/1.5),10)+"' width='470' height='75' frameborder=0></iframe>";
		l_s+="<br /><a href='#' onclick='$p.app.popup.hide()'>"+lg("cancel")+"</a> - <a href='#' onclick=\"rssNewspaper.publish.insertImage("+v_id+",'');\">"+lg("noImg")+"</a>";
		$p.app.popup.show(l_s,500);
	},
	insertImage:function(v_id,v_file)
	{
		if (v_file=='' || v_file.indexOf('.gif')!=-1 || v_file.indexOf('.GIF')!=-1)
		{
			rssNewspaper.publish.article[v_id].image='';
			$p.show('rssnp_artimage'+v_id,'none');
			rssNewspaper.publish.displayImage(rssNewspaper.publish.article[v_id].page);
		}
		else
		{
			rssNewspaper.publish.article[v_id].image=__LOCALFOLDER+"/includes/plugins/rss_newspaper/upload/"+v_file;
			($("rssnp_artimage"+v_id)).src=rssNewspaper.publish.article[v_id].image;
			($("rssnp_artimage"+v_id)).style.width=(rssNewspaper.publish.article[v_id].width-10)+'px';
			$p.show('rssnp_artimage'+v_id,'none');
			$p.app.popup.show(lg("imageImport")+" ...",500);
			setTimeout("rssNewspaper.publish.displayImage("+rssNewspaper.publish.article[v_id].page+")",4000);
			$p.show('rssnp_artimage'+v_id,'block');
		}
	},
	displayImage:function(v_page)
	{
		rssNewspaper.publish.placeArticles(v_page);
	},
	save:function()
	{
		$p.app.popup.show(lg("PDFGeneration")+" ...",400);

		//save the page configuration
		l_var="id="+rssNewspaper.publish.id;

		for (var i=1;i<=rssNewspaper.publish.pageLayout.length;i++)
		{
			l_var+="&l"+i+"="+rssNewspaper.publish.pageLayout[i];
		}
		
		var l_page=1;
		for (var i=0;i<rssNewspaper.publish.article.length;i++)
		{
			l_var+="&f"+i+"="+rssNewspaper.publish.formatForSave(rssNewspaper.publish.article[i].feed);
			l_var+="&t"+i+"="+rssNewspaper.publish.formatForSave(rssNewspaper.publish.article[i].title);
			l_var+="&b"+i+"="+rssNewspaper.publish.formatForSave(rssNewspaper.publish.article[i].body);
			l_var+="&a"+i+"="+rssNewspaper.publish.formatForSave(rssNewspaper.publish.article[i].link);
			l_var+="&d"+i+"="+rssNewspaper.publish.article[i].pubdate.getFullYear()+"-"+(rssNewspaper.publish.article[i].pubdate.getMonth()+1)+"-"+rssNewspaper.publish.article[i].pubdate.getDate();
			l_var+="&p"+i+"="+rssNewspaper.publish.article[i].page;
			l_var+="&y"+i+"="+(parseInt((rssNewspaper.publish.article[i].top+20-rssNewspaper.publish.topMargin)/4.22,10)+10);// adapt to pdf page dimensions (+20 due to article options bar)
			l_var+="&x"+i+"="+(parseInt((rssNewspaper.publish.article[i].left-rssNewspaper.publish.leftMargin)/4.22,10)+5);
			l_var+="&w"+i+"="+parseInt(rssNewspaper.publish.article[i].width/4.22,10);
			//image information
			$p.show("rssnp_art"+i,"block"); // need to be displayed to get the image information
			if (rssNewspaper.publish.article[i].image!="")
			{
				l_var+="&i"+i+"="+rssNewspaper.publish.article[i].image;
				var l_img=$("rssnp_artimage"+i);
				l_var+="&ix"+i+"="+(parseInt((getPos(l_img,"Left")-rssNewspaper.publish.leftMargin)/4.22,10)+5);
				l_var+="&iy"+i+"="+(parseInt((getPos(l_img,"Top")-rssNewspaper.publish.topMargin)/4.22,10)+5);
				l_var+="&iw"+i+"="+(parseInt(l_img.offsetWidth/4.22,10));
				l_var+="&ih"+i+"="+(parseInt(l_img.offsetHeight/4.22,10));
//alert((parseInt((getPos(l_img,"Left")-rssNewspaper.publish.leftMargin)/4.22,10)+5)+"="+(parseInt((getPos(l_img,"Top")-rssNewspaper.publish.topMargin)/4.22,10)+10)+"="+l_img.offsetWidth+" "+l_img.offsetHeight);
			}
			else
			{
				l_var+="&i"+i+"=&ix"+i+"=&iy"+i+"=&iw"+i+"=&ih"+i+"=";
			}
		}

		//generate the PDF file
		executescr(rssNewspaperFolder+"scripts/scr_publish_save.php",l_var,true,false,rssNewspaper.publish.buildPDF);
	},
	formatForSave:function(v_s)
	{
		var l_s=$p.string.esc($p.string.htmlToText(v_s));
		l_s=l_s.replace(/%E2%80%99/g,"'");
		return l_s;
	},
	buildPDF:function(v_id)
	{
		executescr(rssNewspaperFolder+"scripts/scr_build_pdf.php","id="+v_id,true,false,rssNewspaper.publish.openPDF);
	},
	openPDF:function(v_pdfFile)
	{
		//open the PDF file in a new window
		link(rssNewspaperFolder+"/"+v_pdfFile,true)

		$p.app.popup.hide();
		rssNewspaper.loadDashboard();
	}
}