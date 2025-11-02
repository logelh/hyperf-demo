<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Service;

use Exception;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class EmailService
{
    #[Inject]
    protected StdoutLoggerInterface $logger;

    /**
     * 发送邮件.
     */
    public function send(string $to, string $subject, string $content): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = SMTP::DEBUG_OFF; // 关闭调试
            $mail->isSMTP();
            $mail->Host = 'smtp.163.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'logelh@163.com';
            $mail->Password = 'NCjGNKq3Qfwm33mk';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('logelh@163.com', 'Sender');
            $mail->addAddress($to, 'Recipient');

            $mail->Subject = $subject;
            $mail->Body = $content;

            $mail->send();
            $this->logger->info('邮件发送成功!' . $to . ";{$subject};{$content}");
            return true;
        } catch (Exception $e) {
            // TODO 失败邮件需要丢到一个死信队列重试
            $this->logger->error('邮件发送失败!' . $e->getMessage());
            return false;
        }
    }
}
