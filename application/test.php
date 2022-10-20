<?php

$curl_data = array(
    "email_sender" => '{"name":"' . $email . '","email":"' . $email . '"}',
    "email_sender" => '{"name":"' . $email . '","email":"' . $email . '"}',
    "email_sender" => '{"name":"' . $email . '","email":"' . $email . '"}',
    "email_sender" => '{"name":"' . $email . '","email":"' . $email . '"}',

);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://email-api.pitjarus.co/send_mail');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = json_decode(curl_exec($ch));
curl_close($ch);

?>

<script>
    const data = {
        email_sender: `{"name":".email.","email":".$email."}`,
        email_replyto: `{"name":"${email_sender_reply_to_name}","email":"${email_sender_reply_to_address}"}`,
        email_tos: `[{"email" : "${email_to_address}","name":"${email_to_name}"}]`,
        email_subject: subject,
        email_body: htmlContent,
    };
    const r = await axios('https://email-api.pitjarus.co/send_mail', {
        method: 'post',
        data: data,
    });
</script>