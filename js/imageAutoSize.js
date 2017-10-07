jQuery.fn.imageAutoSize = function(width,height)
{
    $("img",this).each(function()
    {
        var image = $(this);
        if(image.width()>width)
        {
            image.height(width/image.width()*image.height());
            image.width(width);
         }
        if(image.height()>height)
        {
            image.width(height/image.height()*image.width());
            image.height(height);
        }
    });
}