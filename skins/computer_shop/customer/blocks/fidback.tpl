<form id="feddback_form" name="form1" class="feddback_form" method="POST" action="{$smarty.server.REQUEST_URI}">
    <label for="name1" class="fb_label">Ваше имя<span class="asterisk"> *</span></label>
    <input name="name1" type="text" id="fb_name" class="input fb_input">
    <label for="e-mail1" class="fb_label">Ваш E-mail<span class="asterisk"> *</span></label>
    <input name="e-mail1" type="text" id="fb_email" class="input fb_input">
    <label for="telephon1" class="fb_label">Контактный телефон</label>
    <input type="text" name="telephon1" id="fb_phone" class="input fb_input">
    <label for="fb_theme" class="fb_label">Тема сообщения<span class="asterisk"> *</span></label>
    <input type="text" id="fb_theme" class="input fb_input">
    <label for="message1" class="fb_label">Ваше сообщение<span class="asterisk"> *</span></label>
    <textarea name="message1" id="fb_message" class="input fb_message"></textarea>
    <div class="clear"></div>

    <input type="hidden" value="pages.view" name="dispatch">
    <input type="hidden" value="1" name="page_id">
    <center><input type="submit" class="submit_btn fb_btn" value="Отправить"></center>
</form>

