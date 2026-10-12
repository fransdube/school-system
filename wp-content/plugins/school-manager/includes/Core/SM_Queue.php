<?php
namespace SchoolManager\Core;

class SM_Queue {
    public static function push($job, $payload) {
        // Skeleton for async queue
        error_log("Job pushed to queue: $job");
    }
}
