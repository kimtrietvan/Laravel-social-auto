@extends('layout.dashboard_basic')

@section('header')
    <style>

        #tab-content form {
            display: none;
        }

        #tab-content form.is-active {
            display: block;
        }

        .file-input {
            position: absolute;
            top: 0;
            right: 0;
            min-width: 100%;
            min-height: 100%;
            font-size: 100px;
            text-align: right;
            opacity: 0;
            filter: alpha(opacity=0);
            outline: none;
            background: white;
            cursor: inherit;
            display: block
        }
        input[type=file]::file-selector-button {
            margin-right: 20px;
            border: none;
            background: #084cdf;
            padding: 3px 5px;
            /*margin-bottom: 50px;*/
            border-radius: 10px;
            color: #fff;
            cursor: pointer;
            transition: background .2s ease-in-out;
        }

        input[type=file]::file-selector-button:hover {
            background: #0d45a5;
        }
    </style>

@endsection

@section('body')
    <div class="columns">
        <div class="column">

{{--            --}}
            <div class="tabs is-centered is-boxed is-fullwidth" id="tabs">
                <ul>
                    <li class="is-active" data-tab="1">
                        <a>
                            <span class="icon is-small"><i class="fa-solid fa-angles-right"></i></span>
                            <span>Quick shot</span>
                        </a>
                    </li>
                    <li data-tab="2">
                        <a>
                            <span class="icon is-small"><i class="fa-regular fa-clock"></i></span>
                            <span>Schedule shot</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div id="tab-content">
{{--                <div class="is-active" data-content="1">--}}
                    <form class="is-active" data-content="1" action="/dashboard/quick_shot" method="post">
                        {{csrf_field()}}
                        <input type="hidden" id="pinterest_enable" name="pinterest_enable" value="0">
                        <div class="field">
                            <label class="label">Message</label>
                            <div class="control">
                                <textarea name="Pinterest_note" class="textarea" placeholder="Textarea"></textarea>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">File upload</label>
                            <div class="control">
                                <label class="radio">
                                    <input type="radio" onchange="upDateInputType()" name="Pinterest_type" value="Image" checked>
                                    Image
                                </label>
                                <label class="radio">
                                    <input type="radio" onchange="upDateInputType()" value="Video" name="Pinterest_type">
                                    Video
                                </label>
                            </div>
                            <div class="control">
                                <input style="display: none" id="inputVideo" name="Pinterest_video" class="input" multiple type="file">
                                <input class="input" name="Pinterest_image" type="text" id="inputURL" placeholder="Image URL">
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Link</label>
                            <div class="control">
                                <input name="Pinterest_link" class="input" placeholder="Link">
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Social</label>
                            <div class="control">
{{--                                --}}
                                <div>
                                <div class="columns">
                                    <div style="margin: auto" class="column is-four-fifths">Pinterest</div>
                                    <div class="column auto"><a onclick="changeTage(this)" data-state="0" data-social="pinterest" class="button is-danger social-network">Off</a></div>
                                </div>
                                <div data-social-account="pinterest" class="social-account" style="display: none">
                                    <div class="field">
                                        <label class="label">
                                            Title
                                        </label>
                                        <input type="text" class="input" name="Pinterest_title">

                                    </div>

                                    <?php
                                        $user_id = Illuminate\Support\Facades\Auth::user()['id'];
                                        $pin_accounts = App\Models\PinterestAccount::where('user_id', $user_id)->get();
                                        foreach ($pin_accounts as $account) {
                                            $boards = App\Models\PinterestBoard::where('pinterest_id', $account['id'])->get();
                                            foreach ($boards as $board) {
                                                echo '<div class="field">';
                                                echo '<label class="checkbox">';
                                                echo '<input type="checkbox" name="Pinterest_board[]" value="'.$board['id'].'">';
                                                echo ' '.$account['username'].' - '.$board['board_name'];
                                                echo '</label>';
                                                echo '</div>';

                                            }
                                        }
                                        ?>
                                </div>
                            </div>
{{--                                --}}
                            </div>
                        </div>
                        <input type="submit" class="button" value="Submit">
                    </form>
{{--                </div>--}}
                <form data-content="2">
                    Music
                </form>

            </div>
{{--            --}}
        </div>
{{--        --}}
        <div class="column">

        </div>
    </div>

@endsection
@section('end_body')
    <script>
        function changeTage(el) {
            // console.log(el)
            if (el.innerText == 'Off') {
                el.innerText = 'On'
                el.classList.remove('is-danger')
                el.classList.add('is-success')
                el.setAttribute('data-state', '1')
                document.getElementById('pinterest_enable').value = '1'
            }
            else {
                el.innerText = 'Off'
                el.classList.add('is-danger')
                el.classList.remove('is-success')
                el.setAttribute('data-state', '0')
                document.getElementById('pinterest_enable').value = '0'
            }

            const state = el.getAttribute('data-state')
            const social = el.getAttribute('data-social')
            document.querySelectorAll('.social-account').forEach((element) => {
                element.style.display = 'none'
                var social_type = element.getAttribute('data-social-account')
                if (social_type == social && state == '1') {
                    element.style.display = 'block'
                }
            })

        }
    </script>
    <script>
        const TABS = [...document.querySelectorAll('#tabs li')];
        const CONTENT = [...document.querySelectorAll('#tab-content form')];
        const ACTIVE_CLASS = 'is-active';

        function initTabs() {
            TABS.forEach((tab) => {
                tab.addEventListener('click', (e) => {
                    let selected = tab.getAttribute('data-tab');
                    updateActiveTab(tab);
                    updateActiveContent(selected);
                })
            })

        }

        function upDateInputType() {
            document.getElementById('inputVideo').style.display = 'none';
            // console.log(document.querySelector('input[name="Pinterest_type"]:checked').value)
            document.getElementById('inputURL').style.display = 'none';
            if (document.querySelector('input[name="Pinterest_type"]:checked').value == 'Image') {
                document.getElementById('inputURL').style.display = 'block';
            }
            else {
                document.getElementById('inputVideo').style.display = 'block';
            }
        }

        function updateActiveTab(selected) {
            TABS.forEach((tab) => {
                if (tab && tab.classList.contains(ACTIVE_CLASS)) {
                    tab.classList.remove(ACTIVE_CLASS);
                }
            });
            selected.classList.add(ACTIVE_CLASS);
        }

        function updateActiveContent(selected) {
            CONTENT.forEach((item) => {
                if (item && item.classList.contains(ACTIVE_CLASS)) {
                    item.classList.remove(ACTIVE_CLASS);
                }
                let data = item.getAttribute('data-content');
                if (data === selected) {
                    item.classList.add(ACTIVE_CLASS);
                }
            });
        }



        window.onload = (event) => {
            initTabs();
            upDateInputType()
        }
    </script>
@endsection
