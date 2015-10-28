<% if $MoreThanOnePage %>
    <div class="pagination">
        <% loop $PaginationSummary(2) %>
            <% if $CurrentBool %>
                <a href="#" class="page active" title="$PageNum">$PageNum</a>
            <% else %>
                <% if $Link %>
                    <a href="$Link" class="page" title="$PageNum">$PageNum</a>
                <% else %>
                    <a href="#" class="page" title="...">...</a>
                <% end_if %>
            <% end_if %>
        <% end_loop %>
    </div>
<% end_if %>