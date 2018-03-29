<div id="accordion" role="tablist" aria-multiselectable="true">
  <div class="card">
    <div class="card-header" role="tab" id="header-params">
      <h6 class="mb-0">
        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#menu-params" aria-expanded="false" aria-controls="header-params">
          Affichage
        </a>
      </h6>
    </div>
    <div id="menu-params" class="pr-4 pl-3 mb-5">
      <div class="card-body">
        <i class="fa fa-window-close float-right" onclick="toggleNav();"></i>
        <button id="btn_save_config" class="btn btn-primary btn-sm m-1">Save config</button> 
      </div>
    </div>
  </div>
</div>

<style>
.sidenav {
    height: 100%;
    width: 0px;
    position: fixed;
    z-index: 1;
    top: 75px;
    right: 0;
    background-color: #888;
    overflow-x: hidden;
    transition: 0.5s;
    padding-top: 60px;
}
.card {
    background-color: #efefef;
}

.sidenav a {
    padding: 8px 8px 8px 32px;
    text-decoration: none;
    font-size: 25px;
    color: #818181;
    display: block;
    transition: 0.3s;
}

.sidenav a:hover, .offcanvas a:focus{
    color: #f1f1f1;
}

.sidenav .closebtn {
    position: absolute;
    top: 0;
    right: 25px;
    font-size: 36px;
    margin-left: 50px;
    color: #f1f1f1;
}

#main {
    transition: margin-left .5s;
    padding: 16px;
}

@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}
</style>