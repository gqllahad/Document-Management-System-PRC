<div class="tools-bottom">
    <div class="filter-buttons">
        <button type="button" class="active" data-filter="all">All projects <span>{{ $counts['all'] }}</span></button>
        <button type="button" data-filter="pending">Pending contract <span>{{ $counts['pending'] }}</span></button>
        <button type="button" data-filter="recorded">Contract recorded <span>{{ $counts['recorded'] }}</span></button>
    </div>

    <div class="search">
        <i class="bi bi-search"></i>
        <input type="search" id="projectSearch" placeholder="Search project..">
    </div>
</div>