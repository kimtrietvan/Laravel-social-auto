@extends('layout.base')

@section('title', 'Schedule')

@section('body')

<form class="is-active" data-content="1" action="" method="post">
    {{csrf_field()}}
    <input type="hidden" id="pinterest_enable" name="pinterest_enable" value="0">
    <input type="hidden" name="product_id" value="{{$product_id}}">
    <div class="field columns is-mobile">
            <div class="column"><label class="label" style="margin: 0;top: 50%;left: 50%;">Schedule</label></div>
            <div class="column"><input class="input" name='minute' type="text" placeholder='Minute'></div>
            <div class="column"><input class="input" name='hours' type="text" placeholder='Hours'></div>
            <div class="column"><input class="input" name="day" type="text" placeholder="Day"></div>
            <div class="column"><input class="input" name='month' type="text" placeholder="Month"></div>
            <div class="column"><input class="input" name='year'type="text" placeholder="Year"></div>
    </div>
    <div class="field">
        {{-- <label class="label"></label> --}}
        <div class="control">
            <p>You can use * for any date. Example: 30 22 * * * mean schedule at 22:30 at every day</p>
        </div>
    </div>
    <div class="field">
        <label class="label">Message</label>
        <div class="control">
            <textarea name="Pinterest_note" class="textarea" placeholder="Textarea"></textarea>
        </div>
    </div>
    <div class="field">
        <label class="label">Media type</label>
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
        {{-- <div class="control">
            <input style="display: none" id="inputVideo" name="Pinterest_video" class="input" multiple type="file">
            <input class="input" name="Pinterest_image" type="text" id="inputURL" placeholder="Image URL">
        </div> --}}
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