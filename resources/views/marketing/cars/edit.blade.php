@extends('layouts.marketing')

@php
  $title = 'Edit Produk Mobil';
  $pageTitle = 'Edit Produk Mobil';
  $action = route('marketing.products.update', $car);
  $method = 'PUT';
@endphp

@include('marketing.cars.form')

