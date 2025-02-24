<?php

nameSpace App\Enums;
enum Status: string
{
    case PENDING = 'pending';
    case PROGRESS = 'progress';
    case COMPLETED = 'completed';
}