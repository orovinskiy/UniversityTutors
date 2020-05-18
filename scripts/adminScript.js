$(".delete").on("click", function () {
    if (confirm("Are you sure you want to delete this admin?")) {

        let id = $(this).data("id");

        console.log(id);

        // Update database via ajax
        $.post("../adminAjax", {
            delete_id: id,
            success: function () {
                window.location.href = ("../admin");
            }
        });

    }
});