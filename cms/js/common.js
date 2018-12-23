function replaceMissingImages(imgElement) {
    imgElement.onerror = "";
    imgElement.src = '../../images/cover-placeholder.jpg';
}