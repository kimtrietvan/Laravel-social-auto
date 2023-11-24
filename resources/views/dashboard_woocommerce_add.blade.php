@extends('layout.dashboard_basic')

@section('title', 'Add woocommerce website')

@section('body')
    <h1 class="title">Add Woocommerce site</h1>
    <form action="{{route('woocommerce_add_site')}}" method="post">
        {{csrf_field()}}
        <div class="field">
            <label class="label">Website URL</label>
            <div class="control">
                <input class="input" name="site" type="text" placeholder="Website URL">
            </div>
        </div>
        <div class="field">
            <label class="label">Consumer key</label>
            <div class="control">
                <input class="input" name="ck" type="text" placeholder="Consumer key">
            </div>
        </div>
        <div class="field">
            <label class="label">Consumer sercet</label>
            <div class="control">
                <input class="input" name="cs" type="text" placeholder="Consumer sercet">
            </div>
        </div>
        <div class="field">
            <div class="control">
                <button class="button is-primary">Submit</button>
            </div>
        </div>
    </form>
@endsection