<?php

namespace App\DTO\Mail;

/**
 * Class MailDTO
 * @package App\DTO\Mail
 */
class MailDTO
{
    const KEY_FROM_EMAIL  = 'fromEmail';
    const KEY_SUBJECT     = 'subject';
    const KEY_BODY        = 'body';
    const KEY_SOURCE      = 'source';
    const KEY_TO_EMAILS   = 'toEmails';
    const KEY_ATTACHMENTS = 'attachments';

    /**
     * @var string $fromEmail
     */
    private string $fromEmail = "";

    /**
     * @var string $subject
     */
    private string $subject = "";

    /**
     * @var string $body
     */
    private string $body = "";

    /**
     * @var string $source
     */
    private string $source = "";

    /**
     * @var array $toEmails
     */
    private array $toEmails = [];

    /**
     * Key is a file name, value is file_content {@see file_get_contents()}
     *
     * @var Array<string> $attachments
     */
    private array $attachments = [];

    /**
     * @return string
     */
    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    /**
     * @param string $fromEmail
     */
    public function setFromEmail(string $fromEmail): void
    {
        $this->fromEmail = $fromEmail;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    /**
     * @return array
     */
    public function getToEmails(): array
    {
        return $this->toEmails;
    }

    /**
     * @param array $toEmails
     */
    public function setToEmails(array $toEmails): void
    {
        $this->toEmails = $toEmails;
    }

    /**
     * @return string[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @param string[] $attachments
     */
    public function setAttachments(array $attachments): void
    {
        $this->attachments = $attachments;
    }

    /**
     * Returns dto data in form of string
     *
     * @return string
     */
    public function toJson(): string
    {
        $dataArray = $this->toArray();
        return json_encode($dataArray);
    }

    /**
     * Returns dto data in form of array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::KEY_FROM_EMAIL  => $this->getFromEmail(),
            self::KEY_SUBJECT     => $this->getSubject(),
            self::KEY_BODY        => $this->getBody(),
            self::KEY_SOURCE      => $this->getSource(),
            self::KEY_TO_EMAILS   => $this->getToEmails(),
            self::KEY_ATTACHMENTS => $this->getAttachments(),
        ];
    }
}