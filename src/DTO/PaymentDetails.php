<?php

namespace ApiChef\PayHere\DTO;

use Carbon\Carbon;

class PaymentDetails
{
    public $statusCode;
    public $data;
    public $paymentId;
    public $orderId;
    public $date;
    public $description;
    public $status;
    public $currency;
    public $amount;
    public $customer;
    public $amountDetail;
    public $paymentMethod;

    public function __construct(array $data)
    {
        $this->statusCode = $data['status'];
        $this->data = $data['data'][0];
        $this->paymentId = $this->data['payment_id'];
        $this->orderId = $this->data['order_id'];
        $this->date = Carbon::parse($this->data['date']);
        $this->description = $this->data['description'];
        $this->status = $this->data['status'];
        $this->currency = $this->data['currency'];
        $this->amount = $this->data['amount'];
        $this->customer = $this->data['customer'];
        $this->amountDetail = $this->data['amount_detail'];
        $this->paymentMethod = $this->data['payment_method'];
    }

    public function getCustomer(): Customer
    {
        return new Customer($this->customer);
    }

    public function getPriceDetails(): PriceDetails
    {
        return new PriceDetails($this->amountDetail);
    }

    public function getPaymentMethod(): PaymentMethod
    {
        return new PaymentMethod($this->paymentMethod);
    }
}
