@extends('backend.layouts.master')

<title>{{ Helper::getSetting()->brand_name }} - Order Detail</title>

@section('main-content')
    <div class="p-4">
    <div class="card">
    <h5 class="card-header">Order       <a href="{{route('order.pdf',$order->id)}}" class=" btn btn-sm btn-primary shadow-sm float-right"><i class="fas fa-download fa-sm text-white-50"></i> Generate PDF</a>
      </h5>
      <div class="card-body">
        @if($order)
        <table class="table table-striped table-hover">
          <thead>
            <tr>
                <th>S.N.</th>
                <th>Order No.</th>
                <th>Name</th>
                <th>Email</th>
                <th>Quantity</th>
                <th>Charge</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
                <td>{{$order->id}}</td>
                <td>{{$order->order_number}}</td>
                <td>{{$order->first_name}} {{$order->last_name}}</td>
                <td>{{$order->email}}</td>
                <td>{{$order->quantity}}</td>
                <td>Rp. {{number_format($order->shipping->price)}}</td>
                <td>Rp. {{number_format($order->total_amount)}}</td>
                <td>
                    @if($order->status=='new')
                      <span class="badge badge-primary">{{$order->status}}</span>
                    @elseif($order->status=='process')
                      <span class="badge badge-warning">{{$order->status}}</span>
                    @elseif($order->status=='delivered')
                      <span class="badge badge-success">{{$order->status}}</span>
                    @else
                      <span class="badge badge-danger">{{$order->status}}</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group" role="group">
                    <a href="{{route('order.edit',$order->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="{{route('order.destroy',[$order->id])}}">
                      @csrf
                      @method('delete')
                          <button class="btn btn-danger btn-sm dltBtn" data-id={{$order->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                    </form>
                    </div>
                </td>

            </tr>
          </tbody>
        </table>

        <section class="confirmation_part section_padding">
          <div class="order_boxes">
            <div class="row">
              <div class="col-lg-6 col-lx-4">
                <div class="order-info">
                  <h4 class="text-center pb-4">SHIPPING INFORMATION</h4>
                    <table class="table">
                        <tr class="">
                            <td>Waybill</td>
                            <td> : {{$order->no_resi}}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td> :  {{$tracking['result']['summary']['status']}}</td>
                        </tr>
                        <tr>
                            <td>Courir</td>
                            <td> : {{$tracking['result']['summary']['courier_name']}}</td>
                        </tr>
                        <tr>
                            <td>Receiver</td>
                            <td> : {{$tracking['result']['delivery_status']['pod_receiver']}}</td>
                        </tr>
                        <tr>
                            <td>Receiver Date</td>
                            <td> : {{$tracking['result']['delivery_status']['pod_date']}}</td>
                        </tr>
                    </table>
                </div>
              </div>

              <div class="col-lg-6 col-lx-4">
                  <h4 class="text-center pb-4">TRACKING INFORMATION</h4>
                  <div class="accordion mt-1" id="accordionExample">
                      @php
                          $groupedManifests = [];
                      @endphp
                      @foreach($tracking['result']['manifest'] as $key => $detail)
                          @php
                              $manifestCode = $detail['manifest_code'];
                              if (!isset($groupedManifests[$manifestCode])) {
                                  $groupedManifests[$manifestCode] = [];
                              }
                              $groupedManifests[$manifestCode][] = $detail;
                          @endphp
                      @endforeach

                      @php
                          $groupedManifests = array_reverse($groupedManifests, true);
                      @endphp

                      @foreach($groupedManifests as $manifestCode => $manifestDetails)
                          @php
                              $manifestDetails = array_reverse($manifestDetails, true);
                          @endphp
                          <div class="card">
                              <div class="card-header" id="heading{{$manifestCode}}">
                                  <h2 class="mb-0">
                                      <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse{{$manifestCode}}" aria-expanded="true" aria-controls="collapseOne">
                                          {{$manifestDetails[0]['manifest_description']}}
                                      </button>
                                  </h2>
                              </div>
                              <div id="collapse{{$manifestCode}}" class="collapse @if($loop->last) show @endif" aria-labelledby="heading{{$manifestCode}}" data-parent="#accordionExample">
                                  <div class="card-body">
                                      @foreach($manifestDetails as $detail)
                                          <table class="mb-2">
                                              <tr>
                                                  <td>Drop Point </td>
                                                  <td> : </td>
                                                  <td>{{$detail['city_name']}}</td>
                                              </tr>
                                              <tr>
                                                  <td>Status</td>
                                                  <td> : </td>
                                                  <td>{{$detail['manifest_description']}}</td>
                                              </tr>
                                              <tr>
                                                  <td>Date</td>
                                                  <td> : </td>
                                                  <td>{{$detail['manifest_date']}}</td>
                                              </tr>
                                              <tr>
                                                  <td>Time</td>
                                                  <td> : </td>
                                                  <td>{{$detail['manifest_time']}}</td>
                                              </tr>
                                          </table>
                                      @endforeach
                                  </div>
                              </div>
                          </div>
                      @endforeach
                  </div>
              </div>
          </div>
        </section>
        @endif

      </div>
    </div>
    </div>
@endsection

@push('styles')
<style>
    .order-info,.shipping-info{
        background:#ECECEC;
        padding:20px;
    }
    .order-info h4,.shipping-info h4{
        text-decoration: underline;
    }

</style>
@endpush
