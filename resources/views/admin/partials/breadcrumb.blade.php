<div class="admin-page-header">
    <div class="admin-page-header__title">
        <h6 class="page-title">{{ __($pageTitle) }}</h6>
    </div>
    <div class="admin-page-header__actions d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins">
        @stack('breadcrumb-plugins')
    </div>
</div>
