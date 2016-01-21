


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