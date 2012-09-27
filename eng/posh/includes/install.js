// ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of POSH (Portaneo Open Source Homepage) http://sourceforge.net/projects/posh/.

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
// ***************************************
// POSH Install specific javascript
//
// be careful, this file must be saved under uft8 format, and display an e accentuated here : é
//
// ***************************************


var $p={}
/*
    Class: $p.install
 
        use while install process

    file: install.js



*/
$p.install={
    displayFormSwitch: function () {

        var form = '\
            <p class="install_active_suite">\
            <input type="checkbox" name="collab_suite" id="name="collab_suite"> <label>'+lg('installActiveCollabSuite')+'</label><br />\
            '+lg("installActiveCollabSuiteDesc")+'\
            </p>\
        ';
        $('colsuite').innerHTML=form;
    },
    changePluginStatus:function(isDisplay) {
        if (isDisplay==1) {
            $('colsuite').style.display='block';
        }
        else {
            document.step1.collab_suite.checked=false;
            $('colsuite').style.display='none';            
        }
    }

}
