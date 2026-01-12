<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f4f4f4; padding: 10px; text-align: center; border-radius: 5px; }
        .content { padding: 20px; background-color: #fff; border: 1px solid #ddd; border-radius: 5px; margin-top: 10px; }
        .footer { font-size: 12px; color: #777; text-align: center; margin-top: 20px; }
        .label { font-weight: bold; color: #555; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Contact Us Inquiry</h2>
        </div>
        <div class="content">
            <p>You have received a new message from your store's contact form.</p>
            
            <p><span class="label">Name:</span> {{ $contactMessage->name }}</p>
            <p><span class="label">Email:</span> {{ $contactMessage->email }}</p>
            <p><span class="label">Subject:</span> {{ $contactMessage->subject }}</p>
            
            <hr>
            
            <p><span class="label">Message:</span></p>
            <p style="white-space: pre-wrap;">{{ $contactMessage->message }}</p>
        </div>
        <div class="footer">
            <p>Sent from your Storefront Contact Form</p>
        </div>
    </div>
</body>
</html>
