<?php
ini_set('display_errors', 0);
$form = array(
  "name" => "我的表單",

  "title" => "表單",

  "req_mark" => "*",

  "error_animate" => 'shake',

  "mailto" => array(
    "nesonchang@gmail.com",
  ),

  "success_massage" => "留言送出成功！我們會儘速回覆您。",

  "item" => array(

    'name' => array(
      'type' => 'text',
      'label' => '姓名',
      'placeholder' => '您的大名',
      'required' => true
    ),

    'email' => array(
      'type' => 'email',
      'label' => 'E-mail',
      'placeholder' => 'E-mail',
      'required' => true
    ),

    'phone' => array(
      'type' => 'text',
      'label' => '聯絡電話',
      'placeholder' => '聯絡電話',
      'required' => false
    ),

    'disabled' => array(
      'type' => 'text',
      'label' => '此欄勿填',
      'placeholder' => '此欄勿填',
      'value' => '0123456789',
      'required' => false,
      'disabled' => true
    ),

    'msg' => array(
      'type' => 'textarea',
      'label' => '留言',
      'placeholder' => '在此留下您的訊息...'
    ),

    'accept' => array(
      'type' => 'checkbox',
      'label' => '',
      'placeholder' => '我接受條款',
      'required' => true,
    ),

    'canmail' => array(
      'type' => 'checkbox',
      'label' => '',
      'placeholder' => '我願意收到促銷訊息',
      'required' => false,
      'description' => '包括限時優惠、商品折扣等等好康。'
    ),

  )

);

if ( isset($_POST['form_send']) ) {
  $success = true;
  if ($success) {
    $header = "Content-type: text/html; charset=UTF-8\n";
    $subject = $form['name'];
    $subject = "=?UTF-8?B?" . base64_encode($subject) . "?=";
    $message = '<table border="0">';
    foreach ($form['item'] as $name => $item) {
      switch ($item['type']) {

        case text:
        case email:
        case textarea:
          $message = $message."<tr><td><b>".$item['label']."　</b></td><td>".$_POST[$name]."</td></tr>";
        break;

        case checkbox:
          if ($_POST[$name] == 'on') $t = 'yes';
          else $t = 'no';
          $message = $message."<tr><td><b>".$item['label']."　</b></td><td>".$item['placeholder'].": ".$t."</td></tr>";
        break;
      }
    }
    $message = $message.'</table>';
    $message = ereg_replace("\n", "</td></tr><tr><td></td><td>", $message);
    if (isset($_POST['email'])) $header = $header."From: ".$_POST['email'];
    foreach($form['mailto'] as $email){
      mail($email, $subject, $message, $header);
    }
  } else {
    $submit_failed = true;
    foreach ($form['item'] as $name => $item) {
      $form['item'][$name]['value'] = $_POST[$name];
    }
  }
}

?><!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $form['title']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" media="screen">
  </head>
  <body>
    <div class="container">
      <?php if ($success) echo '<div class="alert alert-success animated fadeInDown">'.$form['success_massage'].'</div>' ?>
      <form class="form-horizontal well" method="post" onsubmit="return validate_form(this);">
        <fieldset>
          <?php if ($form['title'] != '') echo "<legend>".$form['title']."</legend>" ?>
          <?php
            foreach ($form['item'] as $name => $item) {
              if ($item['required'] == true && $form['req_mark'] != '') $item['label'] = $form['req_mark'].' '.$item['label'];
              switch ($item['type']) {

                case text:
                  echo "<div class=\"control-group ".$name." ".$item['type']."\"><label class=\"control-label\">".$item['label']."</label><div class=\"controls\"><input type=\"text\" name=\"".$name."\" class=\"input-xlarge\" placeholder=\"".$item['placeholder']."\" value=\"".$item['value']."\" ";
                  if ( $item['disabled'] == true ) echo "disabled=\"disabled\"";
                  echo "><span class=\"help-block\">".$item['description']."</span></div></div>";
                break;

                case email:
                  echo "<div class=\"control-group ".$name." ".$item['type']."\"><label class=\"control-label\">".$item['label']."</label><div class=\"controls\"><input type=\"text\" name=\"".$name."\" class=\"input-xlarge\" placeholder=\"".$item['placeholder']."\" value=\"".$item['value']."\" ";
                  if ( $item['disabled'] == true ) echo "disabled=\"disabled\"";
                  echo "><span class=\"help-block\">".$item['description']."</span></div></div>";
                break;

                case checkbox:
                  echo "<div class=\"control-group ".$name." ".$item['type']."\"><label class=\"control-label\">".$item['label']."</label><div class=\"controls\"><label class=\"checkbox inline\"><input type=\"checkbox\" name=\"".$name."\" ";
                  if ( $item['disabled'] == true ) echo "disabled=\"disabled\"";
                  echo "> ".$item['placeholder']."</label><span class=\"help-block\">".$item['description']."</span></div></div>";
                break;

                case textarea:
                  echo "<div class=\"control-group ".$name." ".$item['type']."\"><label class=\"control-label\">".$item['label']."</label><div class=\"controls\"><textarea name=\"".$name."\" class=\"input-xlarge\" rows=\"5\" placeholder=\"".$item['placeholder']."\">".$item['value']."</textarea><span class=\"help-block\">".$item['description']."</span></div></div>";
                break;

              }
            }
          ?>
          <div class="form-actions">
            <input name="form_send" type="hidden" value="1">
            <button type="submit" class="btn btn-primary" data-loading-text="請稍候...">送出</button>
            <button type="reset" class="btn">重填</button>

          </div>
        </fieldset>
      </form>
    </div>
    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
      function validate_email(field, c){
        with (field){
          apos=value.indexOf("@")
          dotpos=value.lastIndexOf(".")
          if (apos<1||dotpos-apos<2){
            $(c).addClass("error");
            $(c).addClass("animated");
            $(c).addClass("<?php echo $form['error_animate']; ?>");
            setTimeout('$(".email").removeClass("<?php echo $form['error_animate']; ?>")', 1000);
            return false
          }else{
            return true
          }
        }
      }

      function validate_required(field, c){
        with (field){
          if (value==null||value==""||value=="false"){
            $(c).addClass("error");
            $(c).addClass("animated");
            $(c).addClass("<?php echo $form['error_animate']; ?>");
            setTimeout('$(".control-group").removeClass("<?php echo $form['error_animate']; ?>")', 1000);
            return false;
          }else{
            return true;
          }
        }
      }

      function validate_check(field, c){
        with (field){
          if (checked != true){
            $(c).addClass("error");
            $(c).addClass("animated");
            $(c).addClass("shake");
            setTimeout('$(".control-group").removeClass("shake")', 1000);
            return false;
          }else{
            return true;
          }
        }
      }

      function validate_form(thisform){
        with (thisform){
          <?php
            foreach ($form['item'] as $name => $item) {
              if ($item['required'] == true) {
                switch ($item['type']) {
                  case 'text':
                    echo "if (validate_required(".$name.", \".".$name."\") == false){\n".$name.".focus();\nreturn false;\n}\n";
                  break;
                  case 'email':
                    echo "if (validate_email(".$name.", \".".$name."\") == false){\n".$name.".focus();\nreturn false;\n}\n";
                  break;
                  case 'checkbox':
                    echo "if (validate_check(".$name.", \".".$name."\") == false){\n".$name.".focus();\nreturn false;\n}\n";
                  break;
                }
              }
            }
          ?>
        }
      }
      <?php
        foreach ($form['item'] as $name => $item) {
          echo "$(\".".$name."\").keydown(function(){\n$(\".".$name."\").removeClass(\"error\");\n});\n";
        }
      ?>
    </script>
  </body>
</html>
