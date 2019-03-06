@extends('layouts.app')

@section('content')
    <script src="https://wpp.wirecard.com/loader/paymentPage.js" type="text/javascript"></script>
    <script src="https://api.wirecard.com/engine/hpp/paymentPageLoader.js" type="text/javascript"></script>
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-6 row">
                <div class="col-md-12"><h5>New approach</h5></div>
                <div class="col-md-12" style="height: 400px;" id="wirecard-payform" data-url="{{ $result['payment-redirect-url'] }}">

                </div>
                <div class="col-md-12">
                    <input class="btn btn-primary" id="wirecard_pay_btn" type="button" onclick="pay()" value="Pay Now"/>
                    <script type="text/javascript">

                        var url = document.getElementById('wirecard-payform').getAttribute("data-url");

                        console.log('redirect URL:', url);

                        // new approach?
                        WPP.seamlessRender({
                            url: url, // this is the payment link returned in response to your initial api/payment/register request from step 1
                            wrappingDivId: "wirecard-payform",
                            onSuccess: function (response) {
                                // called when seamless form is successfully rendered
                            },
                            onError: function (errResp) {
                                // called if seamless form failed to render
                            }
                        });

                        function pay() {
                            WPP.seamlessSubmit({
                                onSuccess: function (response) {
                                    console.log('Success:',response);
                                },
                                onError: function (response) {
                                    console.log('Error:',response);
                                }
                            })
                        }
                    </script>
                </div>
            </div>


        </div>

    </div>

@endsection