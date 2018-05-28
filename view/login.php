
<style>
form{
    margin: 15%;
}
</style>

<form method="POST" class="needs-validation" novalidate>
  <div class="form-group">
    <label for="realName">真实姓名</label>
    <input type="text" class="form-control" name="name" id="realName" aria-describedby="realNameHelp" placeholder="请输入真实姓名" required>
    <small id="realNameHelp" class="form-text text-muted">为了打造无戾气社区</small>
     <div class="invalid-feedback">
        姓名不能为空
      </div>
  </div>
  <div class="form-group">
    <label for="idCardNumber">身份证号码</label>
    <input type="text" class="form-control" name="id_num" id="idCardNumber" placeholder="身份证号码" required>
     <div class="invalid-feedback">
        身份证号码不能为空
      </div>
  </div>
  <div class="form-group form-check">
    <input type="checkbox" class="form-check-input" id="exampleCheck1">
    <label class="form-check-label" for="exampleCheck1">使用密码登录（功能暂时不可用）</label>
  </div>
  <button type="submit" class="btn btn-primary">登录</button>
</form>