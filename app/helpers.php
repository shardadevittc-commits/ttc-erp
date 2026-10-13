<?php
use Illuminate\Support\Carbon;

function _dt($datetime)
{
	$dateTime = Carbon::parse($datetime)->format('d-m-Y h:i A');
    return $dateTime;
}

function _d($date)
{

    $date = Carbon::parse($date)->format('d-m-Y');
    return $date;
}

function _time($time)
{
    $date = Carbon::parse($time)->format('h:i A');
	return $date;
}

function sanitizePhone($phone)
{
    $number = explode('-', $phone, 2);
    $number[0] = str_replace(['-'], '', $number[0]);
    //$phonenumber = str_replace('+'.$number[0], '', $number[1]);

    $phone = str_replace([$number[0], '-'], '', count($number) > 1 ? $number[1] : $phone);
    $phone = str_replace("/[0-9]/g", '', $phone);
    $phone = trim($phone);

    if($phone && substr($phone, 0, 1) == '1')
    {
        $phone = substr($phone, 1, strlen($phone));
    }
    else if($phone)
    {
        $phone = (substr($phone, 0, 1) == 0 ? substr($phone, 1, strlen($phone)) : $phone);
    }
    return $number[0] . '-' . $phone;
}

function _rz($value)
{
    $coorestNumFome = rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.');
    return $coorestNumFome ?? '---';
}
