@extends($activeTemplate.'layouts.frontend')
@section('content')
    @include($activeTemplate.'partials.breadcrumb')

    <div class="section--bg pt-60 pb-60">
        <div class="container">
            <form class="register prevent-double-click" action="" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row justify-content-center">
                    <div class="col-xl-4 col-lg-5">
                        <div class="profile-setting-sidebar">
                            <div class="fileinput fileinput-new " data-provides="fileinput">
                                <div class="fileinput-new thumbnail" data-trigger="fileinput">
                                    <img src="{{ getImage(imagePath()['profile']['user']['path'].'/'. $user->image) }}" alt="@lang('Image')">
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail"></div>
                                <div class="img-input-div">
                                <span class="cmn-btn2 btn-file btn-block overflow-hidden">
                                    <input type="file" name="image" accept=".png, .jpg, .jpeg">
                                </span>
                                </div>
                                <code class="text-muted">@lang('Image size') {{imagePath()['profile']['user']['size']}}</code>
                            </div>
                            <ul class="caption-list mt-4">
                                <li>
                                    <span class="caption">@lang('Username')</span>
                                    <span class="value">{{$user->username}}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Email Address')</span>
                                    <span class="value">{{$user->email}}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Mobile Number')</span>
                                    <span class="value">{{$user->mobile}}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Country')</span>
                                    <span class="value">{{@$user->address->country}}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-7 mt-lg-0 mt-4">
                        <div class="custom--card bg-white">
                            <div class="card-body">
                                <h4 class="mb-4 border-bottom pb-2">@lang('Profile Information')</h4>

                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label for="InputFirstname" class="col-form-label">@lang('First Name'):</label>
                                        <input type="text" class="form-control" id="InputFirstname" name="firstname" placeholder="@lang('First Name')" value="{{$user->firstname}}" minlength="3">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="lastname" class="col-form-label">@lang('Last Name'):</label>
                                        <input type="text" class="form-control" id="lastname" name="lastname" placeholder="@lang('Last Name')" value="{{$user->lastname}}" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label for="address" class="col-form-label">@lang('Address'):</label>
                                        <input type="text" class="form-control" id="address" name="address" placeholder="@lang('Address')" value="{{@$user->address->address}}" required="">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="state" class="col-form-label">@lang('State'):</label>
                                        <input type="text" class="form-control" id="state" name="state" placeholder="@lang('state')" value="{{@$user->address->state}}" required="">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label for="zip" class="col-form-label">@lang('Zip Code'):</label>
                                        <input type="text" class="form-control" id="zip" name="zip" placeholder="@lang('Zip Code')" value="{{@$user->address->zip}}" required="">
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label for="city" class="col-form-label">@lang('City'):</label>
                                        <input type="text" class="form-control" id="city" name="city" placeholder="@lang('City')" value="{{@$user->address->city}}" required="">
                                    </div>
                                </div>
                                <div class="form-group  mb-0 mt-3">
                                    <button type="submit" class="cmn-btn btn-block">@lang('Update Profile')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
