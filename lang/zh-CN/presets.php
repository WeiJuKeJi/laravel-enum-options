<?php

return [
    'payment_method' => [
        'wechat' => '微信支付',
        'alipay' => '支付宝',
        'bank_transfer' => '银行转账',
        'cash' => '现金',
        'credit_card' => '信用卡',
        'debit_card' => '储蓄卡',
        'union_pay' => '银联',
        'paypal' => 'PayPal',
        'apple_pay' => 'Apple Pay',
        'google_pay' => 'Google Pay',
        'pos' => 'POS机',
        'wechat_pos' => '微信POS',
        'other' => '其他',
    ],

    'payment_status' => [
        'unpaid' => '未支付',
        'pending' => '待支付',
        'paying' => '支付中',
        'paid' => '已支付',
        'failed' => '支付失败',
        'cancelled' => '已取消',
        'refunding' => '退款中',
        'refunded' => '已退款',
        'partially_refunded' => '部分退款',
        'timeout' => '支付超时',
    ],

    'refund_status' => [
        'none' => '无退款',
        'pending' => '待退款',
        'processing' => '退款中',
        'partial' => '部分退款',
        'full' => '全额退款',
        'failed' => '退款失败',
        'rejected' => '退款拒绝',
    ],

    'order_status' => [
        'pending' => '待处理',
        'confirmed' => '已确认',
        'processing' => '处理中',
        'completed' => '已完成',
        'cancelled' => '已取消',
        'expired' => '已过期',
        'failed' => '失败',
        'on_hold' => '暂停',
        'refunded' => '已退款',
        'partially_refunded' => '部分退款',
    ],

    'order_type' => [
        'standard' => '普通订单',
        'presale' => '预售订单',
        'group_buy' => '拼团订单',
        'flash_sale' => '秒杀订单',
        'subscription' => '订阅订单',
        'gift' => '礼品订单',
        'exchange' => '兑换订单',
    ],

    'user_status' => [
        'active' => '正常',
        'inactive' => '未激活',
        'suspended' => '已暂停',
        'banned' => '已封禁',
        'deleted' => '已删除',
        'pending_verification' => '待验证',
    ],

    'gender' => [
        'male' => '男',
        'female' => '女',
        'other' => '其他',
        'prefer_not_to_say' => '保密',
    ],

    'approval_status' => [
        'draft' => '草稿',
        'pending' => '待审批',
        'approved' => '已通过',
        'rejected' => '已拒绝',
        'cancelled' => '已取消',
        'revoked' => '已撤销',
    ],

    'publish_status' => [
        'draft' => '草稿',
        'scheduled' => '定时发布',
        'published' => '已发布',
        'unpublished' => '未发布',
        'archived' => '已归档',
    ],
];
