<?php

namespace App\Contracts;

interface MqttPublisherInterface
{
    public function publish(string $topic, string $message, int $qos = 0): bool;
    public function publishSuccess(string $message): bool;
    public function publishError(string $message): bool;
}