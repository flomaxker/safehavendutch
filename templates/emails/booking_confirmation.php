<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { width: 80%; margin: auto; padding: 20px; border: 1px solid #ddd; background: #f9f9f9; }
        .header { background: #0056b3; color: #ffffff; padding: 10px 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 0.8em; color: #555; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Booking Confirmation</h2>
        </div>
        <div class="content">
            <p>Dear <?= htmlspecialchars($userName) ?>,</p>
            <p>Your booking for the following lesson has been confirmed:</p>
            <ul>
                <li><strong>Lesson:</strong> <?= htmlspecialchars($lessonTitle) ?></li>
                <li><strong>Teacher:</strong> <?= htmlspecialchars($teacherName) ?></li>
                <li><strong>Time:</strong> <?= htmlspecialchars($lessonStartTime) ?> - <?= htmlspecialchars($lessonEndTime) ?></li>
                <li><strong>Location:</strong> <?= htmlspecialchars($lessonLocation) ?></li>
            </ul>
            <p>Thank you for your booking!</p>
            <p>Best regards,</p>
            <p>The Safe Haven Dutch Team</p>
        </div>
        <div class="footer">
            <p>&copy; <?= date('Y') ?> Safe Haven Dutch. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
