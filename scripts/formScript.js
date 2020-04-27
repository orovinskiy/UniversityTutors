//Get the file form the user
const inputFile = document.getElementById("img");
const previewContainer = document.getElementById("imagePreview");
const previewImage = previewContainer.querySelector(".image-preview__image");
const previewDefaultText = previewContainer.querySelector(".image-preview__default-text");

inputFile.addEventListener("change", function () {
    const file = this.files[0];
    // console.log(file);
    if (file) {
        const reader = new FileReader();
        previewDefaultText.style.display = "none";
        previewImage.style.display = "block";
        reader.addEventListener("load", function () {
            previewImage.setAttribute("src", this.result);

        });
        reader.readAsDataURL(file);
    }
});