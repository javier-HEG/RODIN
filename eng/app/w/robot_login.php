









<!--

	userLogin.jsp



        login main page



        Author: CONVERA SWITZERLAND, SB

        Date:   07.01.2004

-->















<html>

<head>

  <title>Schweizerisches Bundesarchiv: Online-Amtsdruckschriften</title>



  <link rel='stylesheet' type='text/css' href='scheme/style.css'>

  <link rel='stylesheet' type='text/css' href='scheme/styles_cdbundlight.css'>

  <link rel="shortcut icon" href="/images/favicon.ico" />



  <script language="JavaScript" src="scripts/login.js"></script>

  <script language="JavaScript" src="scripts/util.js"></script>



  <base href="http://www.amtsdruckschriften.bar.admin.ch/userLogin.jsp">

</head>

<body>









  









<!-- Make some params visible to jsp, since include page cannot use c:out.  -->



  

  



  

  



  

  



  

  





<!-- Set outer table height. See comments above.  -->

  

	

  





<form name="loginForm"  id="loginForm"

              action="http://www.amtsdruckschriften.bar.admin.ch/login.do" method="GET"

              

>



<!-- HEADER -->

  







<!--

        blank.jsp

-->





  <td valign="top" class="leftSideCell">





  

  </td>





<!-- OUTER TABLE  -->

  <table width="100%"  cellpadding="0" cellspacing="0" border="0">



	<tr>



	<!-- LEFT SIDE  -->

	  







<!--

        blank.jsp

-->





  <td valign="top" class="leftSideCell">





  

  </td>





	<!-- CONTENT TABLE  -->

	  <td valign="top">

		<table width="100%" cellpadding="0" cellspacing="0" border="0"> 





		<!-- Spacer column and push content down.  -->

		  <tr><td width="10">&#160;</td><td>&#160;</td></tr>



		<!-- SEARCH PANEL -->

		  <tr><td></td><td>

			

				







<!--

        welcome.jsp

-->

<br>



Bitte warten Sie, die Suchoberfl&auml;che wird initialisiert.

  



			

		  </td></tr>



		  <!-- CONVERA / SB <tr><td><img src="images/blank.gif" width="1" height="10" alt=""/></td></tr> -->



		<!--

		BAR / TABS

		  Align thin column bar to top, to align with tabs div that has the scroll bar.

		  Stop line before right edge, so it balances with spacer column.

	-->

		  <tr>

			

		  </tr>

		  <tr><td><img src="images/blank.gif" width="1" height="4" alt=""/></td></tr>



		<!-- PAGE CONTENTS -->

		  <tr><td></td><td valign="top">

		   

				









<!--

	autoLogin.jsp



        redirect page to automaticaly log in the user with

        username: guest and password: guest



        this code is included in userLogin.jsp



        Author: CONVERA SWITZERLAND, SB

        Date:   07.01.2004

-->











<SCRIPT language="javascript">

<!--

location.href = "http://www.amtsdruckschriften.bar.admin.ch/login.do?userId=guest&password=guest";

// -->

</SCRIPT>

<NOSCRIPT>

    <br><br>

    Diese Applikation benˆtigt einen Java Script f‰higen Browser.<br>

    Ihr Browser unterst¸tzt momentan jedoch kein Java Script. <br><br>

    Bitte erlauben Sie Ihrem Browser, Java Script auszuf¸hren und rufen Sie diese Seite nochmals auf.

    

</NOSCRIPT>







		   

		  </td></tr>



	  <!-- Close content table  -->

		</table>



	

	  </td>

          

          





  <!-- Close outer table  -->

	</tr>

	

	

  </table>



          











<!--

	footer.jsp

-->



<!-- CONVERA - SB Start 

  <table class="headerFrame" width="100%" cellpadding="0" cellspacing="0" border="0">

-->











<div id="webFooter">

    <div id="webFooterText">

        <span class="webText">

            Schweizerisches Bundesarchiv (BAR)

        </span>

        <br>

        <a href='mailto:bundesarchiv@bar.admin.ch' class="webText" title='Dieser Link ˆffnet ihr E-Mail-Programm f¸r eine Nachricht an das Bundesarchiv'>

            bundesarchiv@bar.admin.ch

        </a>

        &nbsp;|&nbsp;

        <a href='http://www.disclaimer.admin.ch/' class="webText" title='Rechtliche Grundlagen'>

            Rechtliches

        </a>

    </div>

</div>







  </form>









</body>

</html>

