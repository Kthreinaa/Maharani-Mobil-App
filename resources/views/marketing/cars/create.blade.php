@extends('layouts.marketing')

@php
  $title = 'Tambah Produk Mobil';
  $pageTitle = 'Tambah Produk Mobil';
  $action = route('marketing.products.store');
  $method = null;
  $car = null;
@endphp

@include('marketing.cars.form')

