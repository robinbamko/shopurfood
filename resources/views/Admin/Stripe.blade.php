<html>

<form id="validate_form" action="#">
    <div id="stripedetails" >
        <div class="panel-body" >
            {{-- Show All Payment --}}

                <div class="shipping-method-inner" id="orlay" style="display:block;">
                    <div class="input-field">
                        <div class="form-left">
                            <label>Card Number*</label>
                            <input  placeholder="Enter Card Number" id="card_no"  name="card_no" type="text"  class="error" aria-invalid="true" value="4242424242424242">
                        </div>
                        <div class="form-right">
                            <label>Card Expiry Month</label>
                            <input id="ccExpiryMonth" placeholder="Ex:4"  name="ccExpiryMonth" type="text"  class="error" aria-invalid="true" value="4">
                        </div>
                    </div>

                    <div class="input-field">
                        <div class="form-left">
                            <label>Card Expiry Year</label>
                            <input  placeholder="Ex:2020" id="ccExpiryYear" name="ccExpiryYear" type="text"  class="error" aria-invalid="true"value="2020">
                        </div>
                        <div class="form-right">
                            <label>Cvv Number</label>
                            <input id="cvvNumber" placeholder="Enter Cvv Number"  name="cvvNumber" type="text"  class="error" aria-invalid="true" value="123">
                        </div>
                        <button onclick="fnstripe()">Submit</button>
                    </div>

                    <div class="input-field">
                        <div class="form-left">
                            <label>public key</label>
                            <input  placeholder="Ex:2020" id="pk"  type="text" value="pk_test_xStXO9KIFRBBokDJtnQFu6HE" class="error" aria-invalid="true">
                        </div>
                        <div class="form-right">
                            <label>Secret key</label>
                            <input  placeholder="Ex:2020" id="sk"  type="text" value="sk_test_3JmiK9ZIaGadfn87QSfejFrG" class="error" aria-invalid="true">
                        </div>
                    </div>
                </div>

        </div>
</div>
</form>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

<script>
function fnstripe(){
    var errormsg="";
    if($("#card_no").val()=="")
        errormsg="Please Fill Cardno";

    if($("#ccExpiryMonth").val()=="")
        errormsg="Please Expiry Month";
    if($("#ccExpiryYear").val()=="")
        errormsg="Please Fill Expiry Year";
    if($("#cvvNumber").val()=="")
        errormsg="Please Fill cvvnumber";

    if(errormsg=="") {

        var $form = $('#validate_form');
        Stripe.setPublishableKey('pk_test_kQVJrhSxydSXeXKQ3IUJCT2q');
        Stripe.createToken({
            number: $("#card_no").val(),
            cvc: $("#cvvNumber").val(),
            exp_month: $("#ccExpiryMonth").val(),
            exp_year: $("#ccExpiryYear").val(),
            address_zip: $("#expyear").val()
        }, stripeResponseHandler);

        function stripeResponseHandler(status, response) {
            var $form = $('#validate_form');
            if (response.error) {
                stripePayment();
            } else {
                var token = response.id;
                alert(token);
                $form.append($('<input type="hidden" name="stripeToken" />').val(token));
            }
        }
    }
    else {
        return false;
    }
}
</script>
</html>
