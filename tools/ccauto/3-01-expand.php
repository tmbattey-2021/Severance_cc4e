<?php

use \Tsugi\Core\LTIX;
use \Tsugi\Util\LTI;
use \Tsugi\Util\PDOX;
use \Tsugi\Util\U;
use \Tsugi\Util\Mersenne_Twister;

// Called first
function ccauto_instructions($LAUNCH) {
    return <<< EOF
<b>K&R Exercise 3-1.</b> 
    <p>
    You should write a function called <b>expand(s, t)</b> that copies the string
    <b>s</b> to <b>t</b> expanding newlines and tabs to '\\n' and '\\t' respectively.
    Use a switch statement (it will be a short switch statement).  You can assume
    that the <b>t</b> variable contains enough space.  Make sure to properly terminate
    <b>t</b> with the end-of-string marker '\\0'.
    </p>
EOF
;
}

// Remember to double escape \n as \\n
function ccauto_main($LAUNCH) { 
    return <<< EOF
#include <stdio.h>
int main() {
  char t[1000];
  void expand();
  expand("Hello world", t);
  printf("%s\\n", t);
  expand("Hello world\\n", t);
  printf("%s\\n", t);
  expand("Hello\\tworld\\n", t);
  printf("%s\\n", t);
  expand("Hello\\tworld\\nHave a nice\\tday\\n", t);
  printf("%s\\n", t);
}
EOF
;

}

function ccauto_input($LAUNCH) { return false; }

function ccauto_output($LAUNCH) { 
    return <<< EOF
Hello world
Hello world\\n
Hello\\tworld\\n
Hello\\tworld\\nHave a nice\\tday\\n
EOF
;
}

// Make sure to escape \n as \\n
function ccauto_sample($LAUNCH) {
    return <<< EOF
void expand(s, t)
char s[], t[];
{
  int i, j;
  for(i=0, j=0; s[i]; i++) {
    t[j++] = s[i];
  }
  t[j] = '\\0';
}
EOF
;
}

function ccauto_prohibit($LAUNCH) { 
    GLOBAL $RANDOM_CODE_HOUR, $CHAR_2_10, $LOWER_2_10;
    return array(
        array('else', 'You are to use a switch statement, not an if-then-else.'),
    );
}

function ccauto_require($LAUNCH) { 
    return array (
        array('switch', 'You need to use a switch statement - it is a short switch statement but we need to assess an important learning objective :)'),
    );
}

// Make sure to escape \n as \\n
function ccauto_solution($LAUNCH) {
    return <<< EOF
void expand(s, t)
char s[], t[];
{
  int i, j;
  for(i=0, j=0; s[i]; i++) {
    switch(s[i]) {
    case '\\n':
        t[j++] = '\\\\';
        t[j++] = 'n';
        break;
    case '\\t':
        t[j++] = '\\\\';
        t[j++] = 't';
        break;
    default:
        t[j++] = s[i];
    }
  }
  t[j] = '\\0';
}
EOF
;
}

