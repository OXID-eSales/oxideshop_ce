<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
    <head>
        <title>Exception Error</title>
        <style>
            .errorBox {width: auto; font-size:12px; font-weight:bold; color:#D81F01; margin: 20px; padding: 0; border: none; width: 500px;}
            .errorBox .stackTrace { font-size: 11px; color #000; font-weight: normal; margin: 10px 0; padding: 10px 0; border-top: 2px solid #EED8D2}
            .errorBox .msg { font-size: 14px; color #000; font-weight: normal;}
            .error {padding: 8px 15px 8px 20px;  margin-bottom: 15px; font-size: 12px; color: #4b0b0b; border: 2px solid #fed8d2; background: #ffe7e3;}
            p { font: 12px/12px Trebuchet MS,Tahoma,Verdana,Arial,Helvetica,sans-serif; }
        </style>
    </head>

    <body>
        
        <div class="errorBox" style="width: auto;">
              [{if count($Errors) > 0 && count($Errors.default) > 0}]
              <div class="error">
                  [{foreach from=$Errors.default item=oEr key=key}]
                      <p class="msg">[{$oEr->getOxMessage()}]</p>

                      <p class="stackTrace">[{$oEr->getStackTrace()|nl2br}];</p>
                  [{/foreach}]
              </div>
              [{/if}]          
        </div>
    </body>

</html>