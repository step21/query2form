<?php
/* This is query2form. It takes a query string, converts to usable form, 
   emailing you the results.


    Input: //localhost/query2form/jon@rejon.org

    Input: //localhost/query2form?email=jon@rejon.org&title=Some%20Title

    Output: Bootstrap form


    USAGE: http://service.localhost/query2form/?_replyto=jon@rejon.org&_from=jon@rejon.org&fullname=text&title=text&company=text&email=text&address=text&Sign-with-your-initials=text

*/


ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

// if ( ini_get('display_errors') == -1 ) 


// echo $_SERVER['QUERY_STRING'];

function is_email ($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) && 
        preg_match('/@.+\./', $email);
}

function is_url ($url )
{
    return filter_var($url, FILTER_VALIDATE_URL);
}



$configs = [
    "_from"         =>  '',
    "_replyto"      =>  '',
    "_next"         =>  '',
    "_subject"      =>  'Generic Form',
    /* "_cc"           =>  '', */
    "_success"      =>  'Thanks for submitting your form.',
    "_body"         =>  'Please fill out the following form.',
    "_submit"       =>  'Submit',
    "_link"         =>  '',
    "_linkname"     =>  '',
    /* "_action"       =>  'http://service.localhost/query2email/' */
    "_action"       =>  '#'
];

$requests     = $_REQUEST;
$input_errors = $configs;
foreach ($input_errors as $key => $val)
    $input_errors[$key] = '';

$required_configs = [
    /* "_from", */
    "_replyto"
];

$inputs = array();

function get_generic_error ($key, $value, $msg)
{
    return "<p>ERROR: The $key is set to $value. $msg<p>";
}

foreach($requests as $key => $val)
{
    // unclear if needed
    // $val = urldecode($val);
    switch($key)
    {
        case '_replyto':
            if ( is_email($val) )
                $configs[$key] = $val;
            else
                $input_errors[$key] = 
                    get_generic_error($key,$val,"Its not a valid e-mail address.");
            unset($requests[$key]);
            break;
        case '_next':
            if ( is_url($val) )
                $configs[$key] = $val;
            else
                $input_errors[$key] =
                    get_generic_error($key, $val, "Its not a valid url address.");
            unset($requests[$key]);
            break;
        case '_subject':
        case '_success':
        case '_body':
        case '_submit':
        case '_link':
        case '_linkname':
        case '_action':
            $configs[$key] = $val;
            unset($requests[$key]);
            break;
        default:
            // default it to take anything else and use as a field
            if ( $key ) 
            {
                if ( empty($val) )
                    $inputs[$key] = ucfirst($key);
                else
                    $inputs[$key] = $val;
            }
    }
}


echo <<<END
<!doctype html>
<html lang="en">
<head>
END;
echo '    <title>' . $configs['_subject'] . '</title>';
echo <<<END
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
</head>

<body style="margin: 30px;">

END;


if ( empty($_REQUEST) || count($inputs) == 0 )
{
    echo "<h2>Nothing to see...@TODO add docs here\n</h2>";
    exit;
}




$requirements_met = true;
foreach ($required_configs as $config)
{
    if ( empty($configs[$config]))
    {
        $input_errors[$config] = 
            get_generic_error($config,'\'\'',"The parameter is empty.");
        $requirements_met = false;
    }
}

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

echo '    <h2>' . $configs['_subject'] . '</h2>' . "\n";
echo '    <div class="alert alert-info">' . 
         '<p>' . $configs['_body'] . '</p>' .
         ( !empty($configs['_link']) ? 
         '<br /><form action="' . $configs['_link'] . 
         '"><button type="submit" class="btn btn-primary btn-sm">' . 
         ( !empty($configs['_linkname']) ? $configs['_linkname'] : 'File' ) . 
         '</button></form>' : '') . 
         '</div>' . "\n";

echo '    <form method="GET" action="' . 
          ( $configs['_action'] ? $configs['_action'] : '#' ). '">' . "\n";

echo '    <input type="hidden" name="_replyto" value="' . 
          $configs['_replyto'] . '" />' . "\n";

foreach( $inputs as $key => $val )
{
    $val = strtr( $val, '-', ' ');

echo '        <div class="form-group">' . "\n";
echo '            <label for="' . $key . '">' . ucfirst($key) . '</label>' . "\n";
echo '            <input name="' . $key . '" type="name" class="form-control" id="' . $key . '" placeholder="Enter ' . ucfirst($key) . '" value="' . $val . '">' . "\n";
echo '        </div>' . "\n";
}

echo '        <button type="' . (empty($configs['_action']) ? 'button' : 'submit' ) . '" class="btn btn-primary btnr" ' . 
              (empty($configs['_action']) ? 'data-toggle="modal" data-target="#myModal"' : '') . '>' . 
    ($configs['_submit'] ? $configs['_submit'] : "Submit") . '</button>' . "\n";
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


    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>
END;


