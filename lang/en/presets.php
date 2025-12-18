<?php

return [
    'payment_method' => [
        'wechat' => 'WeChat Pay',
        'alipay' => 'Alipay',
        'bank_transfer' => 'Bank Transfer',
        'cash' => 'Cash',
        'credit_card' => 'Credit Card',
        'debit_card' => 'Debit Card',
        'union_pay' => 'UnionPay',
        'paypal' => 'PayPal',
        'apple_pay' => 'Apple Pay',
        'google_pay' => 'Google Pay',
        'pos' => 'POS',
        'wechat_pos' => 'WeChat POS',
        'other' => 'Other',
    ],

    'payment_status' => [
        'unpaid' => 'Unpaid',
        'pending' => 'Pending',
        'paying' => 'Paying',
        'paid' => 'Paid',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled',
        'refunding' => 'Refunding',
        'refunded' => 'Refunded',
        'partially_refunded' => 'Partially Refunded',
        'timeout' => 'Timeout',
    ],

    'refund_status' => [
        'none' => 'No Refund',
        'pending' => 'Pending',
        'processing' => 'Processing',
        'partial' => 'Partial Refund',
        'full' => 'Full Refund',
        'failed' => 'Failed',
        'rejected' => 'Rejected',
    ],

    'order_status' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'expired' => 'Expired',
        'failed' => 'Failed',
        'on_hold' => 'On Hold',
        'refunded' => 'Refunded',
        'partially_refunded' => 'Partially Refunded',
    ],

    'order_type' => [
        'standard' => 'Standard Order',
        'presale' => 'Pre-sale Order',
        'group_buy' => 'Group Buy',
        'flash_sale' => 'Flash Sale',
        'subscription' => 'Subscription',
        'gift' => 'Gift',
        'exchange' => 'Exchange',
    ],

    'user_status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
        'banned' => 'Banned',
        'deleted' => 'Deleted',
        'pending_verification' => 'Pending Verification',
    ],

    'gender' => [
        'male' => 'Male',
        'female' => 'Female',
        'other' => 'Other',
        'prefer_not_to_say' => 'Prefer Not to Say',
    ],

    'approval_status' => [
        'draft' => 'Draft',
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
        'revoked' => 'Revoked',
    ],

    'publish_status' => [
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'published' => 'Published',
        'unpublished' => 'Unpublished',
        'archived' => 'Archived',
    ],
];
