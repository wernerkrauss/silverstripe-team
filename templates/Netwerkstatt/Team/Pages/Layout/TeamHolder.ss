<% include SideBar %>
<div class="content-container unit size3of4 lastUnit">
    <article>
        <h1>$Title</h1>

        <div class="content">
			$Content
			<% if $PaginatedItems %>

                <div>
					<% loop $PaginatedItems %>
                        <h2><a href="$Link">$Name</a></h2>

                        <p>$PortraitPhoto.setWidth(150)</p>

                        <p>$Position</p>

                        <p>$Description</p>

                        <p>Tel: $Tel</p>

                        <p>E-Mail: <a href="mailto:$Email">$Email</a></p>

					<% end_loop %>
                </div>


				<% with $PaginatedItems %>
					<% include Pagination %>
				<% end_with %>
			<% end_if %>
        </div>
    </article>
	$Form
	$CommentsForm
</div>
