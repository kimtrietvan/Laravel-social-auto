<?php 
    use App\Models\WoocommerceProduct;
    use App\Models\Woocommerce;
    use App\Models\WoocommerceProductImage;

?>
@extends('layout.dashboard_basic')

@section('title', "Products")

@section('header')

@endsection

@section('body')
    @foreach (Woocommerce::all() as $site)
        <div class="card">
            <header class="card-header">
                <p class="card-header-title">
                    {{$site->base_url}}
                </p>
                <a href="#" class="card-header-icon" aria-label="more options" data-site='{{$site->id}}' onclick="toggleSiteModal(this,'detailsModal')">
                    <span class="icon">
                        <i class="fas fa-angle-down" aria-hidden="true"></i>
                    </span>
                </a>
            </header>
        </div>
            <div class="modal detailsModal" data-site='{{$site->id}}'>
                <div class="modal-background"></div>
                <div style="width: 70%" class="modal-card">
                    <header class="modal-card-head">
                        <p class="modal-card-title">Products about {{$site->base_url}}</p>
                        <button class="delete" aria-label="close" data-site='{{$site->id}}' onclick="toggleSiteModal(this, 'detailsModal')"></button>
                    </header>
                    <section class="modal-card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product Name</th>
                                    <th>Link</th>
                                    <th>View Image</th>
                                    <th>Schedule</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (WoocommerceProduct::where('woocommerce_id', $site->id)->get() as $product)
                                <tr>
                                    <td>{{$product->product_code}}</td>
                                    <td>{{$product->product_name}}</td>
                                    <td><a href="{{$product->product_url}}">{{$product->product_url}}</a></td>
                                    <td><button class="button view-image" data-product='{{$product->id}}' onclick="toggleImageModal(this, 'imageModal')">View Image</button></td>
                                    <td><button class="button view-image" data-product='{{$product->id}}' onclick="toggleScheduleProduct(this)">Schedule</button></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </section>
                </div>
            </div>
            @foreach (WoocommerceProduct::where('woocommerce_id', $site->id)->get() as $product)
                <div data-product='{{$product->id}}' class="modal imageModal">
                    <div class="modal-background"></div>
                    <div class="modal-content">
                        <p class="image">
                            @foreach (WoocommerceProductImage::where('product_id', $product->id)->get() as $image)
                                <img data-src="{{$image->product_image}}" src="" alt="Placeholder image">
                            @endforeach
                        </p>
                    </div>
                    <button class="modal-close is-large" aria-label="close" data-product='{{$product->id}}' onclick="toggleImageModal(this, 'imageModal')"></button>
                </div>
                <div data-product='{{$product->id}}' class="modal event-modal">
                    <div class="modal-background"></div>
                    <div class="modal-card">
                        <header class="modal-card-head">
                        <p class="modal-card-title">Tạo sự kiện</p>
                        <button class="delete" data-product="{{$product->id}}" aria-label="close" onclick="toggleScheduleProduct(this)"></button>
                        </header>
                        <section class="modal-card-body">
                        <!-- Content -->
                        <div class="field">
                            <label class="label">Tin nhắn</label>
                            <div class="control">
                            <input class="input" type="text" placeholder="Nhập tin nhắn...">
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Loại nội dung</label>
                            <div class="control">
                            <label class="checkbox">
                                <input name="typePost" type="checkbox">
                                Hình ảnh
                            </label>
                            </div>
                            <div class="control">
                            <label class="checkbox">
                                <input name="typePost" type="checkbox">
                                Video
                            </label>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Link</label>
                            <div class="control">
                            <input class="input" type="text" placeholder="Nhập link...">
                            </div>
                        </div>
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label class="label">Schedule</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <div class="control">
                                        <input class="input" type="text" name="minute" placeholder="Minute">
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="control">
                                        <input class="input" type="text" name="hour" placeholder="Hours">
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="control">
                                        <input class="input" type="text" name="day" placeholder="Day">
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="control">
                                        <input class="input" type="text" name="month" placeholder="Month">
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="control">
                                        <input class="input" type="text" name="year" placeholder="Year">
                                    </div>
                                </div>
                            </div>
                    </section>
                        
                    

                        <section style="max-height: 40%; overflow: auto;" class="modal-card-body">
                            
                            
                        </section>
                        <footer class="modal-card-foot">
                        <button class="button is-success" data-product="{{$product->id}}" onclick="toggleScheduleProduct(this)">Lưu</button>
                        <button class="button" data-product="{{$product->id}}" onclick="toggleScheduleProduct(this)">Hủy</button>
                        </footer>
                    </div>
                </div>
            @endforeach
            
    @endforeach


    <script>
        function toggleScheduleProduct(el) {
            var product_id = el.dataset.product;
            window.open("/dashboard/woocommerce/product/schedule/" + product_id , "", "width=200,height=100");
        }
        function toggleSiteModal(el, modalId) {
            var modals = document.getElementsByClassName(modalId);
            for (let i = 0; i < modals.length; i++) {
                if (!('site' in modals[i].dataset)) continue;
                if (modals[i].dataset.site == el.dataset.site) modals[i].classList.toggle("is-active");
            }
        }
        function toggleImageModal(el, modalId) {
            var modals = document.getElementsByClassName(modalId);
            for (let i = 0; i < modals.length; i++) {
                if (!('product' in modals[i].dataset)) continue;
                if (modals[i].dataset.product == el.dataset.product) {
                    modals[i].classList.toggle("is-active");
                    var images = modals[i].getElementsByTagName('img');
                    for (let j = 0; j < images.length; j++) {
                        images[j].src = images[j].dataset.src
                    }
                }
            }
        }
    </script>
@endsection