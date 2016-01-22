


<div class="user-auth">
    <script>
        function userAuth(){
            beforeAjax();
            $.ajax({
                type: 'post',
                url: '/api/user-auth',
                data: $("#user-auth-form").serialize(),
                dataType: 'json',
                success: function (data) {
                    afterAjax();
                    alert(data.msg);
                },
                error: function () {
                    afterAjax();
                    alert("ajax 错误");
                }
            });

        }


        function bindBank(){
            beforeAjax();
            $.ajax({
                type: 'post',
                url: '/api/bind-bank',
                data: $("#bind-bank-form").serialize(),
                dataType: 'json',
                success: function (data) {
                    afterAjax();
                    alert(data.msg);
                },
                error: function () {
                    afterAjax();
                    alert("ajax 错误");
                }
            });
        }
    </script>
    <form id="user-auth-form">
        <div class="input-item">
            <label>姓名:</label><input name="name" />
        </div>
        <div  class="input-item">
            <label>身份证号:</label><input name="idCardNo">
        </div>
        <div class="input-item">
            <span class="btn" onclick="userAuth()">提交</span>
        </div>


    </form>
</div>



<div class="bind-bank">
    <form id="bind-bank-form">
        <div class="input-item">
            <label>开户行:</label><input name="BankCode" />
        </div>
        <div class="input-item">
            <label>银行卡号:</label><input name="CardNo" />
        </div>
        <div  class="input-item">
            <label>预留手机号:</label><input name="phone">
        </div>
        <div class="input-item">
            <label>开户地区:</label><input name="CityCode" />
        </div>
        <div class="input-item">
            <span class="btn" onclick="bindBank()">提交</span>
        </div>


    </form>


</div>