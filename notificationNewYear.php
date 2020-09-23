<div class="wrapperNotice" id="wrapperNotice">
    <div class="contentNotice">
      <div class="bgOver"></div>
      <div class="wpclose" onclick="closeNotice()">X</div>
    </div>
    <div class="bodyNotice">
    Please notice that Omegatheme will be closed for business from January 23, 2020 to January 29, 2020 for the Lunar New Year holiday. Therefore, there will likely be a delay in supporting your installed application. We will resume work on January 30, 2020.
If you encounter any problem, please temporarily disable the app and we will check and solve it as soon as our holiday end.
    </div>
</div>
<style>
.wrapperNotice{
    position: fixed;
    bottom: 5px;
    z-index: 999;
    width: 50%;
    margin: auto;
    left: 25%; 
    box-shadow: -1px 2px 4px 1px #00000054; 
    border-radius: 5px; 
} 
@media only screen and (max-width: 600px) {
    .wrapperNotice{
    
    width: 100%;
    
    left: 0%; 
   
} 
}
.contentNotice{
    background-image: url("https://windy.omegatheme.com/banner.jpg");
    opacity:1;
    height: 85px;
    border-radius: 5px;
    padding: 5px 15px;

}
.wpclose {
    position: absolute;
    right: 3px;
    top: -2px;
    color: #fff;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    z-index: 9999;
}
.bgOver{
    position: absolute;
    width: 100%;
    height: 100%;
    background: #27272780;
    left: 0px;
    top: 0px;
    border-radius: 5px;
}
.bodyNotice{
    position: absolute;
    top: 0px;
    text-align: center;
    line-height: 25px;
    color: #fff;
    padding: 5px 15px;
}
</style>
<script>
function closeNotice(){
    var x = document.getElementById("wrapperNotice");
    x.style.display = "none";
}
</script>