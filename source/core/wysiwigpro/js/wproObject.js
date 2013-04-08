/*
 * (c) Copyright Chris Bolt 2007 and forever thereafter, All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */
if (typeof(wproObject)=='undefined') {
	wproObject = {
		htmlSpecialChars:function (str) {
			return String(str).replace(/&/gi, '&amp;').replace(/\xA0/gi, '&nbsp;').replace(/</gi, '&lt;').replace(/>/gi, '&gt;').replace(/"/gi, '&quot;')	
		},
		serializeMediaToTag:function(data) {
			var str = '';
			var allowedEmpty = /^(title|alt)$/i
			if (data['object']) {
				str += '<object';
				for(var x in data['object']) {
					if (data['object'][x]=='') if (!allowedEmpty.test(x)) continue;
					str	+= ' '+x+'="'+this.htmlSpecialChars(data['object'][x])+'"';
				}
				str+='>';
				if (data['param']) {
					for(var x in data['param']) {
						str+='\n<param name="'+x+'" value="'+this.htmlSpecialChars(data['param'][x])+'"';
						str+=' />';	
					}
				}
			}
			if (data['embed']) {
				str += '<embed';
				for(var x in data['embed']) {
					if (data['embed'][x]=='') if (!allowedEmpty.test(x)) continue;
					str	+= ' '+x+'="'+this.htmlSpecialChars(data['embed'][x])+'"';
				}
				str+='>';
				if (data['content']) {
					str +='<noembed>'+data['content']+'</noembed>';	
				}
				str+='</embed>';
			}
			if (data['content']&&!data['embed']) {
				str+=data['content'];	
			}
			if (data['object']) {
				str+='</object>';
			}
			return str;
		},
		write:function(data) {
			document.write(this.serializeMediaToTag(data));
		}
	}
}