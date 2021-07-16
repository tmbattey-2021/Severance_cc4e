<?php
if ( !isset($_COOKIE['secret']) || $_COOKIE['secret'] != '42' ) {
    header("Location: index.php");
    return;
}

use \Tsugi\Core\LTIX;
use \Tsugi\Util\U;

if ( ! isset($CFG) ) {
    if (!defined('COOKIE_SESSION')) define('COOKIE_SESSION', true);
    require_once "tsugi/config.php";
    $LAUNCH = LTIX::session_start();
}

if ( U::get($_SESSION,'id', null) === null ) {
    $_SESSION['error'] = 'Must be logged in to test code';
    header("Location: index.php");
    return;
}

require_once "sandbox/sandbox.php";

$stdout = False;
$stderr = False;

if ( isset($_POST['code']) ) {
    unset($_SESSION['retval']);
    $_SESSION['code'] = U::get($_POST, 'code', false);
    $_SESSION['input'] = U::get($_POST, 'input', false);
    header("Location: play.php");
    return;
}

$sample = U::get($_REQUEST, 'sample');
// if ( ! preg_match('/^[a-zA-Z_0-9]+.md$/', $sample) ) $sample = false;
if ( is_string($sample) ) {
    unset($_SESSION['code']);
    unset($_SESSION['input']);
    unset($_SESSION['retval']);
    $code = file_get_contents('book/code/'.$sample);
    $retval = null;
    $input = null;  /* TODO - Get this as well */
} else {
    $code = U::get($_SESSION, 'code');
    $input = U::get($_SESSION, 'input');
    $retval = U::get($_SESSION, 'retval');
    if ( $retval == NULL && is_string($code) && strlen($code) > 0 ) {
        $retval = cc4e_compile($code, $input);
        $_SESSION['retval'] = $retval;
    }
}

$lines = $code ? count(explode("\n", $code)) : 15;

?><html>
<head>
<title>CC4E - Code Playground</title>
<link href="static/codemirror-5.62.0/lib/codemirror.css" rel="stylesheet"/>
<style>
body {
    font-family: Courier, monospace;
}

.CodeMirror { height: auto; border: 1px solid #ddd; }
.CodeMirror-scroll { max-height: <?= intval(($lines/13)*20) ?>em; }
</style>
</head>
<body>
<p>
This the <a href="index.php">www.cc4e.com</a> code playground for writing C programs.
<p>
<form method="post">
<p>
<input type="submit" value="Run Code">
<script>
if ( window.opener ) {
    document.write('<button onclick="window.close();">Close</button>');
} else {
    document.write('<input type="submit" onclick="window.location=\'index.php\'; return false;" value="Back to CC4E">');
}
</script>
</p>
<?php
if ( isset($retval->reject) && is_string($retval->reject) ) echo('<p style="color:red;">'.htmlentities($retval->reject).'</p>'."\n");
if ( isset($retval->minimum) && $retval->minimum === false ) echo('<p style="color:red;">Your program did not produce any output</p>'."\n");
if ( isset($retval->allowed) && $retval->allowed === false ) echo('<p style="color:red;">Your program used a disallowed function</p>'."\n");
$compiler = $retval->assembly->stderr ?? false;
if ( is_string($compiler) && strlen($compiler) > 0 ) {
    echo '<pre style="color:red;">'."\n";
    echo "Compiler errors:\n\n";
    echo(htmlentities($compiler, ENT_NOQUOTES));
    echo("</pre>\n");
}
?>
<p>
<textarea id="mycode" name="code" rows="<?= $lines ?>" style="height: <?= $lines ?>em; border: 1px black solid;">
<?php
if ( is_string($code) ) {
    echo(htmlentities($code));
} else {
?>
#include <stdio.h>

main() {
  printf("Hello World\n");
}
<?php } ?>
</textarea>
</p>
<?php
if ( isset($retval->docker->stdout) ) {
    echo '<pre style="color: blue">'."\n";
    echo "Program output:\n\n";
    echo(htmlentities($retval->docker->stdout, ENT_NOQUOTES));
    echo("</pre>\n");
}
?>
<p>Input to your program:</p>
<p>
<textarea id="myinput" name="input" style="width:90%; border: 1px black solid;">
<?php
if ( is_string($input) ) {
    echo(htmlentities($input));
} ?>
</textarea>
</p>
</form>
<p>This code page is under construction and has extensive security filters.  If you think
that it is blocking code it should allow, please add a note in the 
<a href="<?= $CFG->apphome ?>/discussions">Discussions</a>
area.
</p>
<?php if ( is_object($retval) ) { ?>
<pre style="display:none;" id="debug">
Debug output:
<?php
echo(htmlentities(json_encode($retval, JSON_PRETTY_PRINT), ENT_NOQUOTES));

if ( isset($retval->assembly->stdout) && is_string($retval->assembly->stdout) ) {
    echo("\n\n------ Assembly --------\n");
    echo(htmlentities($retval->assembly->stdout, ENT_NOQUOTES));
}
?>
</pre>
<?php } ?>
<script type="text/javascript" src="static/codemirror-5.62.0/lib/codemirror.js"></script>
<script type="text/javascript" src="static/codemirror-5.62.0/mode/clike/clike.js"></script>
<script>
  var myTextarea = document.getElementById("mycode");
  var editor = CodeMirror.fromTextArea(myTextarea, {
        lineNumbers: true,
        matchBrackets: true,
        mode: "text/x-csrc"
  });
</script>
</body>
