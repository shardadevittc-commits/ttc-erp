if(document.querySelector(".ajax-listing-loop")) {
    let listingManage = {
        debounceTimer: null,
        requestController: null,

        init() {
            document.body.addEventListener("input", this.getData);
            document.body.addEventListener("click", this.handleClick);
        },

        handleClick(e) {
            const sortIcon = e.target.closest(".listing-block [data-field]");
            if(sortIcon) {
                e.preventDefault();
                const params = new URLSearchParams(window.location.search);
                const currentSort = params.get("sort_by") || "id";
                const currentOrder = params.get("sort_order") || "desc";
                const nextOrder = currentSort === sortIcon.dataset.field && currentOrder === "asc" ? "desc" : "asc";
                params.set("sort_by", sortIcon.dataset.field);
                params.set("sort_order", nextOrder);
                params.set("page", "1");
                listingManage.load(params);
                return;
            }

            const pageLink = e.target.closest(".listing-pagination a");
            if(pageLink) {
                e.preventDefault();
                const pageUrl = new URL(pageLink.href, window.location.href);
                listingManage.load(new URLSearchParams(pageUrl.search));
            }
        },

        getData(e) {
            if(!e.target.classList.contains("listing-search")) {
                return;
            }

            clearTimeout(listingManage.debounceTimer);
            listingManage.debounceTimer = setTimeout(() => {
                const params = new URLSearchParams(window.location.search);
                const search = e.target.value.trim();
                if(search) {
                    params.set("search", search);
                } else {
                    params.delete("search");
                }
                params.set("page", "1");
                listingManage.load(params);
            }, 300);
        },

        load(params) {
            const url = new URL(window.location.href);
            url.search = params.toString();
            window.history.replaceState(null, "", url.toString());

            if(listingManage.requestController) {
                listingManage.requestController.abort();
            }

            const listing = document.querySelector(".ajax-listing-loop");
            const loading = listing.querySelector(".listing-loading");
            const error = listing.querySelector(".listing-error");
            loading.classList.remove("d-none");
            error.classList.add("d-none");
            listing.setAttribute("aria-busy", "true");
            listingManage.updateSortIndicators(params);

            const requestController = new AbortController();
            listingManage.requestController = requestController;

            fetch(url.toString(), {
                headers: {
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                },
                signal: requestController.signal
            })
                .then(response => {
                    if(!response.ok) {
                        throw new Error("Request failed");
                    }
                    return response.json();
                })
                .then(resp => {
                    if(!resp.status) {
                        error.textContent = "Unable to load sizes. Please try again.";
                        error.classList.remove("d-none");
                        return;
                    }
                    listing.querySelector(".table_listing_body").innerHTML = resp.html;
                    listing.querySelector(".listing-pagination").innerHTML = resp.pagination;
                })
                .catch(requestError => {
                    if(requestError.name !== "AbortError") {
                        error.textContent = "Failed to load sizes. Check your connection and try again.";
                        error.classList.remove("d-none");
                    }
                })
                .finally(() => {
                    if(listingManage.requestController !== requestController) {
                        return;
                    }
                    loading.classList.add("d-none");
                    listing.removeAttribute("aria-busy");
                });
        },

        updateSortIndicators(params) {
            const sortBy = params.get("sort_by") || "id";
            const sortOrder = params.get("sort_order") || "desc";
            document.querySelectorAll(".ajax-listing-loop [data-field]").forEach(icon => {
                const active = icon.dataset.field === sortBy;
                icon.className = active
                    ? `fas ${sortOrder === "asc" ? "fa-sort-down" : "fa-sort-up"} active`
                    : "fas fa-sort";
            });
        }
    };

    listingManage.init();
} else if(document.querySelector(".table_listing_body")) {
    let listingManage = {
        ajaxCall: null,
        debounceTimer: null,
        listingBody: null,
        loading: false,
        page: 1,
        hasMorePages: true,
        listingAjaxCall: null,

        init() {
            this.listingBody = ".table_listing_body";
            document.body.addEventListener("keyup", this.getData);

            window.addEventListener("scroll", function () {
                if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 50) {
                    if(!listingManage.loading && listingManage.hasMorePages) {
                        listingManage.getPaginateData();
                    }
                }
            });
        },

        getPaginateData() {
            if(listingManage.listingAjaxCall && listingManage.listingAjaxCall.readyState != 4) {
                listingManage.listingAjaxCall.abort();
            }

            listingManage.loading = true;
            listingManage.page++;

            let url = new URL(window.location.href);
            url.searchParams.set("page", listingManage.page);

            listingManage.listingAjaxCall = $.ajax({
                url: url,
                success(resp) {
                    if(resp.status) {
                        if(resp.isLastPage) {
                            listingManage.hasMorePages = false;
                        }
                        document.querySelector(".table_listing_body").insertAdjacentHTML("beforeend", resp.html);
                    }
                },
                error() {
                    Swal.fire({ title: 'Failed to load data. Please refresh.', icon: 'error' });
                },
                complete() {
                    listingManage.loading = false;
                }
            });
        },

        getData(e) {
            if(!e.target.classList.contains("listing-search")) {
                return false;
            }

            let data = {
                search: e.target.value,
            };
            clearTimeout(listingManage.debounceTimer);

            listingManage.debounceTimer = setTimeout(() => {
                listingManage.setUrl(data);

                $.ajax({
                    url: current_url,
                    data: data,
                    success(resp) {
                        if(resp.status) {
                            document.querySelector(".table_listing_body").innerHTML = resp.html;
                        }
                    },
                    error() {
                        Swal.fire({ title: 'Failed to load data. Please refresh.', icon: 'error' });
                    }
                });
            }, 300);
        },

        setUrl(param) {
            const url = new URL(window.location.href);

            for(let key in param) {
                url.searchParams.set(key, param[key]);
            }

            window.history.replaceState(null, null, url.toString());
        }
    };

    listingManage.init();
}