@extends('front.layout')

@push('css')
<style>
.account-profile:hover {
  color:#203c70;
  cursor:pointer;
}
</style>
@endpush

@section('content')
<center>
@if(session('error'))
    <div class="alert alert-danger" style="width:350px;margin-top:20px;">
        {{ session('error') }}
    </div>
@endif
@if(session('success'))
    <div class="alert alert-success" style="width:350px;margin-top:20px;">
        {{ session('success') }}
    </div>
@endif

<ul class="list-group" style="width:350px;margin-top:50px;margin-bottom:50px;">
  @foreach ($accounts as $account)
  <li class="list-group-item d-flex align-items-center justify-content-between account-profile">
      <div class="d-flex align-items-center">
          <img src="{{ $account->userprofile }}" class="rounded-circle me-3" style="height:50px;width:50px;" alt="avatar">
          {{ $account->name }}
      </div>
      <div>
          @if(auth()->guard('customer')->check() && auth()->guard('customer')->user()->id == $account->id)
              <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-danger">Logout</button>
              </form>
          @else
              <a href="{{ route('remove-account', $account->id) }}" class="btn btn-sm btn-outline-danger">Remove</a>
          @endif
      </div>
  </li>
  @endforeach
  <li class="list-group-item text-center">
      <a href="{{ route('add-new-account') }}" style="color:#203c70;font-weight:600;">
        + Add New Account
      </a>
  </li>
</ul>
</center>
@endsection

@push('js')

@endpush