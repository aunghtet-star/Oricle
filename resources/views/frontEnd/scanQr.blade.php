@extends('frontEnd.layouts.app')
@section('title','Scan QR')
@section('extra_css')
    <style>
        body {
            background: #EDEDF5;
            font-family: "Oswald", sans-serif;
        }

        .bottom-menu a {
            text-decoration: none;
        }

        .header-menu a {
            text-decoration: none;
        }

    </style>
@endsection
@section('content')
<div class="container scanPay">
    <div class="card">
        <div class="card-body">
            <div class="text-center">
                <div class="card-body">
                    <img src="{{asset('/img/QRcode.png')}}" alt="">
                </div>
                <p class="mb-3">Click button,put QR code in the field</p>
                <button  class="btn btn-theme" data-toggle="modal" data-target="#scanModal">Scan</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="scanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Scan and Pay</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <video id="scanner"></video>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-sm" id="close" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
</div>
@endsection
@section('scripts')
<script src="{{asset('/js/qr-scanner.umd.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
   <script>
        QrScanner.WORKER_PATH = '{{asset('/js/qr-scanner-worker.min.js')}}';

       $(document).ready(function(){
            
            var videoElem = document.getElementById('scanner');
            var qrScanner = new QrScanner(videoElem, function(result){
                if(result){
                    qrScanner.stop();
                    $('#scanModal').modal('hide');
                    
                    var to_phone = result;
                    window.location.replace(`/scan_and_pay?to_phone=${to_phone}`);
                }
                console.log(result); 
            });
            
            $("#scanModal").on("shown.bs.modal", function () { 
                qrScanner.start();
            });

            $('#scanModal').on("hidden.bs.modal", function (e) { 
                qrScanner.stop();
            });
            
            
       });
       
    </script> 
@endsection