<?php
/* This is query2form. It takes a query string, converts to usable form, 
   emailing you the results.

    Input: //localhost/query2form/jon@rejon.org

    Input: //localhost/query2form?email=jon@rejon.org&title=Some%20Title

    Output: Bootstrap form

    USAGE: http://service.localhost/query2form/?_replyto=jon@rejon.org&_from=jon@rejon.org&fullname=text&title=text&company=text&email=text&address=text&Sign-with-your-initials=text

*/


include_once '../lib/config.php';
include_once '../lib/utils.php';
include_once '../lib/shared.php';
include_once '../lib/header.php';




foreach ($input_errors as $key => $error)
{
    echo $error;
}

if ( ! $requirements_met )
{
    echo "<h4>REQUIREMENTS NOT MET</h4>\n";
    exit;
}


/* FORM LAYOUT */

if ( !empty($configs['_body']) )
{
echo '    <div class="alert alert-info">' . 
         '<p>' . $configs['_body'] . '</p>' .
         ( !empty($configs['_link']) ? 
         '<br /><form action="' . $configs['_link'] . 
         '"><button type="submit" class="btn btn-primary btn-sm">' . 
         ( !empty($configs['_linkname']) ? $configs['_linkname'] : 'File' ) . 
         '</button></form>' : '') . 
         '</div>' . "\n";
}

// action is now more a list of actions
$now_action = '';
if ( is_array($configs['_action']) )
    $now_action = $configs['_action'][0] ;
else
    $now_action = $configs['_action'];

echo '    <form method="GET" action="' . 
          ( $now_action ? $now_action : '#' ). '">' . "\n";

/// increment number of actions printed globally
$actions_printed++;

if ( is_array($configs['_action']) )
{
    for ($ct=1; $ct< count($configs['_action']); $ct++)
    {
        echo '    <input type="hidden" name="_action[]" value="' . 
                    $configs['_action'][$ct] . '" />' . "\n";
    }

}
echo '    <input type="hidden" name="_id" value="' . 
          $configs['_id'] . '" />' . "\n";
echo '    <input type="hidden" name="_next" value="' . 
          $configs['_next'] . '" />' . "\n";

echo '    <input type="hidden" name="_replyto" value="' . 
          $configs['_replyto'] . '" />' . "\n";
echo '    <input type="hidden" name="_time" id="_time" value="' . 
          $configs['_time'] . '" />' . "\n";
echo '    <input type="hidden" name="_subject" value="' . 
          $configs['_subject'] . '" />' . "\n";
echo '    <input type="hidden" name="_body" value="' . 
          $configs['_body'] . '" />' . "\n";
echo '    <input type="hidden" name="_submit" value="' . 
          $configs['_submit'] . '" />' . "\n";
echo '    <input type="hidden" name="_success" value="' . 
          $configs['_success'] . '" />' . "\n";
echo '    <input type="hidden" name="_link" value="' . 
          $configs['_link'] . '" />' . "\n";
echo '    <input type="hidden" name="_linkname" value="' . 
          $configs['_linkname'] . '" />' . "\n";
echo '    <input type="hidden" name="_processurl" value="' . 
          $configs['_processurl'] . '" />' . "\n";

// dumper($inputs);
foreach( $inputs as $key => $val )
{

    $input_type = substr( $key, -2 );


    switch ( substr( $key, -2 ) )
    {
        case '_t':
            $input_type = 'textarea';
            $placeholder = substr( $key, 0, -2 );
            break;
        case '_h':
            $input_type = 'hidden';
            $placeholder = substr( $key, 0, -2 );
            break;
        case '_s':
            $input_type = 'special';
            $placeholder = substr( $key, 0, -2 );
            break;
        default:
            $placeholder = $key;
            if ( is_array($val) )
                $input_type = 'radio';
            else
                $input_type = 'text';
    }

    $placeholder = ucwords(strtr( $placeholder, '-', ' '));
    // $val = strtr( $val, '-', ' ');
    $submit = ($configs['_submit'] ? 
        ucwords(strtr( $configs['_submit'], '-', ' ')) : "Submit" );


$spacer = '            ';
switch ( $input_type )
{
    case 'textarea':
        echo $spacer . '<div class="form-group">' . "\n";
        echo $spacer . '<label for="' . $key . '">' . 
            $placeholder . '</label>' . "\n";
        echo $spacer . '<textarea name="' . $key . 
                      '" class="input-block-level form-control" id="' . $key . 
                      '" placeholder="Enter ' . $placeholder . '">' . 
                      $val . '</textarea>' . "\n";
        echo $spacer . '</div>' . "\n";
        break;
    case 'special':
    case 'hidden':
        echo $spacer . '<input type="hidden" name="' . $key . 
                      '" class="form-control" id="' . $key . 
                      '" value="' . $val . '" />' . "\n";
        break;
    case 'radio':
        $ct = 0;
        echo $spacer . '<div class="form-group">' . "\n";
        echo $spacer . '<label for="' . $key . '">' . 
            $placeholder . '</label>' . "<br />\n";
        foreach ( $val as $k => $v )
        {
            echo $spacer . '<input type="radio" name="' . $key . '" ' .
                           ( ( 0 == $ct ) ? 'checked="checked" ' : '' ) .
                           'value="' . $v . '">' . ucwords($v) . "</input>\n";
            $ct++;
        }
        echo $spacer . '</div>' . "\n";
        break;
    case 'text':
    default:
        echo $spacer . '<div class="form-group">' . "\n";
        echo $spacer . '<label for="' . $key . '">' . 
            $placeholder . '</label>' . "\n";
        echo $spacer . '<input name="' . $key . 
                      '" type="name" class="input-block-level form-control" ' .                       'id="' . $key . 
                      '" placeholder="Enter ' . $placeholder . '" value="' . 
                      $val . '">' . "\n";
        echo $spacer . '</div>' . "\n";
    
}

}

echo '        <div class="form-group">' . "\n";
echo '        <button type="' . (empty($configs['_action']) ? 'button' : 'submit' ) . '" class="btn btn-primary btnr" '. 
/* ' onclick="$(\'#_time\').attr(\'value\', getNow())"' .  */
              (empty($configs['_action']) ? 'data-toggle="modal" data-target="#myModal"' : '') . '>' . 
    $submit . '</button>' . "\n";
echo '         </div>' . "\n";
echo <<<END
    </form>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Success</h4>
      </div>
      <div class="modal-body">
END;
if ( !empty($configs['_success']) )
    echo "<p>" . $configs['_success'] . "</p>\n";
echo <<<END
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
        <button type="button" class="btn btn-primary">Close</button>
      </div>
    </div>
  </div>
</div>

END;
include_once '../lib/footer.php';
