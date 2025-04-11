// Set up PHPMailer
                $mail = new PHPMailer(true);
                try {
                    //Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';  // SMTP server
                    $mail->SMTPAuth = true;
                    $mail->Username = 'np03cs4a220505@heraldcollege.edu.np';  // Your Gmail address
                    $mail->Password = 'wbqq vffc nzay lnvm';  // The app-specific password generated in your Google Account
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Encryption type
                    $mail->Port = 25;  // Port number for Gmail SMTP

                    //Recipients
                    $mail->setFrom('np03cs4a220505@heraldcollege.edu.np', 'Guide Me');
                    $mail->addAddress($email);  // Recipient's email

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Email Verification Code';
                    $mail->Body    = 'Your verification code is: ' . $verification_code;

                    // Send email
                    $mail->send();
                    header("Location: emailVeri.php");
                    exit;
                } catch (Exception $e) {
                    $error = "Error sending verification email: " . $mail->ErrorInfo;
                }
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
