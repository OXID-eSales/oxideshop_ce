/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */
var oxid = {
    admin : {
        changeLanguage : function()
        {
            var oSearch = top.basefrm.list.document.getElementById( "search" );
            oSearch.language.value = oSearch.changelang.value;
            oSearch.editlanguage.value = oSearch.changelang.value;
            oSearch.submit();

            var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
            oTransfer.innerHTML += '<input type="hidden" name="language" value="'+oSearch.changelang.value+'">';
            oTransfer.innerHTML += '<input type="hidden" name="editlanguage" value="'+oSearch.changelang.value+'">';

            //forci ng edit frame to reload after submit
            top.forceReloadingEditFrame();
        },

        editThis : function( sID )
        {
            var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
            oTransfer.oxid.value = sID;
            oTransfer.cl.value = top.oxid.admin.getClass( sID );

            //forcing edit frame to reload after submit
            top.forceReloadingEditFrame();

            var oSearch = top.basefrm.list.document.getElementById( "search" );
            oSearch.oxid.value = sID;
            oSearch.submit();
        },

        deleteThis : function( sID )
        {
            var blCheck = window.confirm( top.oxid.admin.getDeleteMessage() );
            if( blCheck == true ) {
                var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
                oTransfer.oxid.value = '-1';
                oTransfer.cl.value = top.oxid.admin.getClass( -1 );

                //forcing edit frame to reload after submit
                top.forceReloadingEditFrame();

                var oSearch = top.basefrm.list.document.getElementById( "search" );
                oSearch.oxid.value = sID;
                oSearch.fnc.value = 'deleteentry';
                oSearch.submit();
            }
        },

        getDeleteMessage : function()
        {
            if ( top.basefrm.list.sDeleteMessage ) {
                return top.basefrm.list.sDeleteMessage;
            } else if ( top.basefrm.edit.sDeleteMessage ) {
                return top.basefrm.edit.sDeleteMessage;
            }
            return '';
        },

        getClass : function( sID )
        {
            if ( top.basefrm.list.sDefClass && top.basefrm.list.sActClass ) {
                return ( sID == -1 || sID == '-1' ) ? top.basefrm.list.sDefClass : top.basefrm.list.sActClass;
            } else if ( top.basefrm.edit.sDefClass && top.basefrm.edit.sActClass ) {
                return ( sID == -1 || sID == '-1' ) ? top.basefrm.edit.sDefClass : top.basefrm.edit.sActClass;
            }
            return '';
        },

        getUnassignMessage : function()
        {
            if ( top.basefrm.list.sUnassignMessage ) {
                return top.basefrm.list.sUnassignMessage;
            } else if ( top.basefrm.edit.sUnassignMessage ) {
                return top.basefrm.edit.sUnassignMessage;
            }
            return '';
        },

        changeEditBar : function( sLocation, sPos, sFunction)
        {
            var oSearch = top.basefrm.list.document.getElementById( "search" );
            oSearch.actedit.value = sPos;
            oSearch.submit();

            var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
            oTransfer.cl.value = sLocation;
            if (sFunction) {
                oTransfer.fnc.value = sFunction;
            }

            //forcing edit frame to reload after submit
            top.forceReloadingEditFrame();
        },

        updateList : function( sID )
        {
            var oSearch = top.basefrm.list.document.getElementById( "search" );
            oSearch.oxid.value = sID;
            oSearch.submit();
        },

        reloadNavigation : function( sID )
        {
            var oNavigation = top.document.getElementById("navigation");
            oNavigation.src = oNavigation.src + "&shp=" + sID;
        },

        changeLstrt : function()
        {
            var oSearch = top.basefrm.list.document.getElementById( "search" );
            if ( oSearch != null && oSearch.lstrt != null ) {
                oSearch.lstrt.value = 0
            }
        },

        getLockTarget : function()
        {
            return top.basefrm.edit.document.getElementById( "oLockTarget" );
        },

        getLockedButton : function()
        {
            return top.basefrm.edit.document.getElementById( "oLockButton" );
        },

        unlockSave : function()
        {
            var oLockedButton = top.oxid.admin.getLockedButton();
            var oLockTarget   = top.oxid.admin.getLockTarget();
            if ( oLockedButton != null && oLockTarget != null ) {
                if ( oLockTarget.value ) {
                    oLockedButton.disabled = false;
                } else {
                    oLockedButton.disabled = true;
                }
            }
        },

        changeListSize : function()
        {
            top.basefrm.document.showlist.submit();
        },

        unassignThis : function( sID )
        {
            var blCheck = confirm( top.oxid.admin.getUnassignMessage() );
            if ( blCheck == true ) {
                var oSearch = top.basefrm.list.document.getElementById( "search" );
                oSearch.oxid.value = sID;
                oSearch.fnc.value = 'unassignentry';
                oSearch.actedit.value = 0;
                oSearch.submit();

                var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
                oTransfer.oxid.value = '-1';
                oTransfer.cl.value = top.oxid.admin.getClass();

                //forcing edit frame to reload after submit
                top.forceReloadingEditFrame();
            }
        },

        setSorting : function( oForm, sTable, sColumn, sDirection )
        {
            // resetting previous
            var aInputs = oForm.getElementsByTagName('input');
            for ( var i = 0; i < aInputs.length; i++ ) {
                if( aInputs[i].getAttribute( "name" ).match( /^sort/ ) ) {
                    oForm.removeChild( aInputs[i] );
                    i--;
                }
            }

            // creating form element
            var oFormField = document.createElement( "input" );
            oFormField.setAttribute( "type", "hidden" );
            oFormField.setAttribute( "name", "sort[" + sTable + "][" + sColumn + "]" );
            oFormField.setAttribute( "value", sDirection );

            // appending..
            oForm.appendChild( oFormField );
        }
    }
};