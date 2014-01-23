<?php

namespace Behat\Mink\Driver\NodeJS\Server;

use Behat\Mink\Driver\NodeJS\Connection;
use Behat\Mink\Driver\NodeJS\Server;

class ZombieServer extends Server
{
    protected function doEvalJS(Connection $conn, $str, $returnType = 'js')
    {
        $result = null;
        switch ($returnType) {
            case 'js':
                $result = $conn->socketSend($str);
                break;
            case 'json':
                $result = json_decode($conn->socketSend("stream.end(JSON.stringify({$str}))"));
                break;
            default:
                break;
        }

        return $result;
    }

    protected function getServerScript()
    {
        $js = <<<'JS'
var net      = require('net')
  , zombie   = require('%modules_path%zombie')
  , browser  = null
  , pointers = []
  , buffer   = ""
  , host     = '%host%'
  , port     = %port%;

var versionCompare = function(v1, v2, op) {
  var normalize = function(versionString) {
    return versionString
      .split(".")
      .map(function(digit) { return parseInt(digit, 10) })
      .reduce(function(previousValue, currentValue, index, arr){
        return previousValue + currentValue*Math.pow(10000, arr.length-index);
      }, 0);
  };

  return eval(normalize(v1) + " " + op + " " + normalize(v2));
}

var zombieVersion = require('%modules_path%zombie/package').version;
if (false == versionCompare("1.4.1", zombieVersion, ">=")) {
  throw new Error("Your zombie.js version is not compatible with this driver. Please use a version <= 1.4.1");
}

net.createServer(function (stream) {
  stream.setEncoding('utf8');
  stream.allowHalfOpen = true;

  stream.on('data', function (data) {
    buffer += data;
  });

  stream.on('end', function () {
    if (browser == null) {
      browser = new zombie.Browser();

      // Clean up old pointers
      pointers = [];
    }

    eval(buffer);
    buffer = "";
  });
}).listen(port, host, function() {
  console.log('server started on ' + host + ':' + port);
});
JS;

        return $js;
    }
}
