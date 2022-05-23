$(document).ready(function() {
    var showDog = localStorage.getItem("showDog");

    if (showDog) {
        var qolpeturl = "<?php echo getDog() ?>";
        var text = "meow!";
    } else {
        var qolpeturl = "<?php echo getCat() ?>";
        var text = "woof!";
    }
    document.getElementById("qol-pet").src = qolpeturl;
    document.getElementById("changePet").textContent = text;

});



$(".changePet").on("click", function() {
    if (document.getElementById("changePet").textContent == "Switch to Doggo") {
        localStorage.setItem("showDog", true);
        var qolpeturl = "<?php echo getDog() ?>";
        var text = "meow!";
    } else {
        localStorage.setItem("showDog", false);
        var qolpeturl = "<?php echo getCat() ?>";
        var text = "woof!";
    }
    document.getElementById("qol-pet").src = qolpeturl;
    document.getElementById("changePet").textContent = text;
    return false;
});