(function() {

    var builder = this.ClaroResourceInterfaceBuilder = {};

    ClaroResourceInterfaceBuilder.menu = {};

    builder.builder = function(
        div,
        divForm,
        selectType,
        submitButton,
        downloadButton,
        cutButton,
        copyButton,
        deleteButton,
        pasteButton,
        closeButton,
        flatChkBox,
        resourceGetter,
        resourceFilter
        )
        {
        var construct = {};

        construct.div = div;
        construct.divForm = divForm;
        construct.selectType = selectType;
        construct.submitButton = submitButton;
        construct.downloadButton = downloadButton;
        construct.cutButton = cutButton;
        construct.copyButton = copyButton;
        construct.deleteButton = deleteButton;
        construct.pasteButton = pasteButton;
        construct.closeButton = closeButton;
        construct.flatChkBox = flatChkBox;
        construct.resourceGetter = resourceGetter;
        construct.resourceFilter = resourceFilter;
        construct.selectedIds = {};
        construct.pasteIds = {};
        construct.cpd = null;
        construct.activePagerItem = 1;
        construct.pager = null;
        construct.nbPage = 0;
        //sets the resource filter callbacks
        resourceFilter.setCallBackToFilter(function(data){
            construct.div.empty();
            var templates = resourceGetter.getTemplates();
            var html = Twig.render(templates.listTemplate, {'instances':data,'webRoot':resourceGetter.getWebRoot()});
            construct.div.append(html);
            setMenu(construct);
        });

        resourceFilter.setCallResetFilter(function(data){
             resourceGetter.getRoots(function(data){appendThumbnails(data, construct)});
        })

        ClaroUtils.sendRequest(
            Routing.generate('claro_resource_menus'),
            function(data) {
                builder.menu = JSON.parse(data);
                for (var menu in builder.menu) {
                    delete builder.menu[menu].items['new'];
                }
            },
            function() {
                resourceGetter.getRoots(function(data){appendThumbnails(data, construct)});
            });

        $('.link-navigate-instance', div).live('click', function(e){
            navigate($(this).parents('.res-block').attr('data-id'), construct);
        });

        $('.breadcrumb-link', div).live('click', function(e){
            navigate($(this).attr('data-id'), construct);
        });

        window.onresize = function(e) {
            resizeBreadcrumb(construct);
            if (construct.pager !== null){
                construct.pager.remove();
            }
            construct.pager = ClaroUtils.renderPager(construct.nbPages, construct.activePagerItem, 'instance', construct.div);
            setPagerActions(construct);
        }

        submitButton.on('click', function(e){
            ClaroUtils.sendRequest(
                Routing.generate('claro_resource_creation_form', {
                    'resourceType': selectType.val()
                }),
                function(data) {
                    divForm.empty().append(data);
                    divForm.find('form').submit(function(e) {
                        e.preventDefault();
                        var parameters = {};
                        parameters.id = $(".breadcrumb-link", div).last().attr('data-id');
                        var action = divForm.find('form').attr('action');
                        action = action.replace('_instanceId', parameters.id)
                        var id = divForm.find('form').attr('id');
                        ClaroUtils.sendForm(action, document.getElementById(id), function(xhr){
                            submissionHandler(xhr, parameters, construct);
                        });
                    })
                    setLayout(construct);
                })
        });

        cutButton.on('click', function(e){
            construct.pasteIds = {};
            construct.pasteIds = getSelectedItems(construct);
            setLayout(construct);
            construct.cpd = 0;
        })

        copyButton.on('click', function(e){
            construct.pasteIds = {};
            construct.pasteIds = getSelectedItems(construct);
            setLayout(construct);
            construct.cpd = 1;
        })

        downloadButton.on('click', function(e){
            var ids = getSelectedItems(construct);
            window.location = Routing.generate('claro_multi_export', ids);
        })

        deleteButton.on('click', function(e){
            var params = getSelectedItems(construct);
            var route = Routing.generate('claro_resource_multi_delete', params);
            ClaroUtils.sendRequest(route, function(data, textstatus, xhr){
                if (204 === xhr.status) {
                    $('.chk-instance:checked', div).each(function(index, element){
                        $(this).parents('.res-block').remove();
                    })
                }
            });
        })

        pasteButton.on('click', function(e){
            var params = {};
            var route = '';
            params = construct.pasteIds;
            if (construct.cpd == 0) {
                params.newParentId = $(".breadcrumb-link", div).last().attr('data-id');
                route = Routing.generate('claro_resource_multimove', params);
                ClaroUtils.sendRequest(route, function(){
                    reload(construct);
                    });
            } else {
                params.instanceDestinationId = $(".breadcrumb-link", div).last().attr('data-id');
                route = Routing.generate('claro_resource_multi_add_workspace', params);
                ClaroUtils.sendRequest(route, function(){
                    reload(construct);
                });
            }
        })

        closeButton.on('click', function(e){
            construct.divForm.empty();
            setLayout(construct);
        })

        $('.chk-instance', div).live('change', function(e){
            var ids = {};
            var i = 0;
            $('.chk-instance:checked', div).each(function(index, element){
                ids[i] = element.value;
                i++;
            })
            construct.selectedIds = ids;
            setLayout(construct);
        })

        flatChkBox.on('change', function(e){
            if(e.target.checked) {
                setLayout(construct);
                var route = Routing.generate('claro_resource_count_instances');
                ClaroUtils.sendRequest(route,
                    function(count){
                        construct.nbPages = count;
                        construct.div.empty();
                        rendersFlatPaginatedThumbnails(construct);
                    });
            } else {
                construct.pager.remove();
                resourceGetter.getRoots(function(data){appendThumbnails(data, construct)});
            }
        })

        return  {
            //return the construct object
            getBuilder:function() {
                return construct;
            }
        }
    }

    function setPagerActions(construct){
        $('.instance-paginator-item').on('click', function(e){
            construct.activePagerItem = e.target.innerHTML;
            rendersFlatPaginatedThumbnails(construct);
        });

        $('.instance-paginator-next-item').on('click', function(e){
            construct.activePagerItem++;
            rendersFlatPaginatedThumbnails(construct);
        })

        $('.instance-paginator-prev-item').on('click', function(e){
            construct.activePagerItem--;
            rendersFlatPaginatedThumbnails(construct);
        })
    }

    function navigate(id, construct) {
        construct.divForm.empty();
        construct.resourceGetter.getChildren(id, function(data){
              appendThumbnails(data, construct);
        })
    }

    function reload(construct){
        var id = $(".breadcrumb-link", construct.div).last().attr('data-id');
        navigate(id, construct);
    }

    function rendersFlatPaginatedThumbnails(construct) {
        if (construct.pager !== null){
            construct.pager.remove();
        }
        construct.pager = ClaroUtils.renderPager(construct.nbPages, construct.activePagerItem, 'instance', construct.div);
        setPagerActions(construct);
        construct.resourceGetter.getFlatPaginatedThumbnails(construct.activePagerItem, function(data){
            $('.res-block').remove();
            construct.div.prepend(data);
            setMenu(construct);
            $(".res-name").each(function(){
                formatResName($(this), 2, 20)
            });
        })
    }

    function getSelectedItems(construct)
    {
        return construct.selectedIds;
    }

    function setMenu(construct)
    {
        //destroy menus

        $('.dropdown').each(function(index, element){
            var resSpan =  $(this).parents('.res-block', construct.div);
            var parameters = {};
            parameters.id = resSpan.attr('data-id')
            parameters.resourceId = resSpan.attr('data-resource_id');
            parameters.type = resSpan.attr('data-type');
            bindContextMenu(parameters, element, construct);
        });

        $('.dropdown-toggle').dropdown();
    }

    /* Cut the name of the resource if its length is more than maxLength,
     * adding '...' at the end. And cut multilines, trying to cut between words when possible. */
    function formatResName(element, maxLines, maxLengthPerLine) {
        maxLines = typeof maxLines !== 'undefined' ? maxLines : 2;
        maxLengthPerLine = typeof maxLengthPerLine !== 'undefined' ? maxLengthPerLine : 20;
        if (typeof element !== 'undefined' && element.text() !== 'undefined'
            && element.text().length > maxLengthPerLine) {
            var newText = new Array(maxLines);
            var curLine = 0;
            var curText = element.text();
            while (curText.length > 0 && curLine < maxLines) {
                newText[curLine] = curText.substr(0, maxLengthPerLine);
                if (curLine == maxLines-1) {
                } else {
                    var i = newText[curLine].length;
                    while (i>0) {
                        var c = newText[curLine].charAt(i-1);
                        if ( !((c>='a' && c<='z') || (c>='A' && c<='Z') || (c>='0' && c<='9')) )
                            break;
                        i--;
                    }
                    if (i > 0)
                        newText[curLine] = newText[curLine].substr(0,i);
                    curText = curText.substr(newText[curLine].length, curText.length);
                    newText[curLine] = newText[curLine]+"<br>";
                }
                curLine++;
            }
            if (curText.length > 0) {
                if (newText[curLine-1].length > maxLengthPerLine-3) {
                    newText[curLine-1] = newText[curLine-1].substr(0, maxLengthPerLine-3);
                    newText[curLine-1] = newText[curLine-1]+"...";
                }
            }
            element.html(newText.join(""));
        }
    };

    function appendThumbnails(data, construct) {
        construct.div.empty();
        construct.div.append(data);
        setMenu(construct);
        setLayout(construct);
        resizeBreadcrumb(construct);
        $(".res-name").each(function(){formatResName($(this), 2, 20)});
    }

    function buildMenu(type, name)
    {
        var html = '<a class="dropdown-toggle" role="button" data-toggle="dropdown" data-target="#" href="#">'+name+'</a>'
        html += '<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">';

        for (var i in builder.menu[type]['items']) {
            html += '<li><a tabindex="-1" href="#">'+i+'</a></li>';
        }

        html += '</ul>';

        return html;
    }

    function bindContextMenu(parameters, menuElement, construct) {
        var type = parameters.type;
        var name = menuElement.innerHTML;
        var html = buildMenu(type, name);
        menuElement.innerHTML = html;

        $('a', $(menuElement).find('.dropdown-menu')).each(function(index, element){
            $(element).on('click', function(e){
                findMenuObject(builder.menu[type], parameters, element.innerHTML, construct)
            });
        });
    };

    //Finds wich menu was fired for a node.
    //@params items is the menu object used.
    function findMenuObject(items, parameters, menuItem, construct)
    {
        for (var property in items.items) {
            if (property == menuItem) {
                executeMenuActions(items.items[property], parameters, construct);
            } else {
                if (items.items[property].hasOwnProperty('items')) {
                    findMenuObject(items.items[property], parameters, menuItem, construct);
                }
            }
        }
    };

    function executeMenuActions (obj, parameters, construct)
    {
        //Removes the placeholders in the route
        var route = obj.route;
        var compiledRoute = route.replace('_instanceId', parameters.id);
        compiledRoute = compiledRoute.replace('_resourceId', parameters.resourceId);
        obj.async ? executeAsync(obj, parameters, compiledRoute, construct) : window.location = compiledRoute;
    }

    function executeAsync(obj, parameters, route, construct) {
        //Delete was a special case as every node can be removed.
        (obj.name === 'delete') ? removeNode(parameters, route, construct) : executeRequest(parameters, route, construct);
    };

    function removeNode(parameters, route, construct) {
        ClaroUtils.sendRequest(route, function(data, textStatus, jqXHR) {
            if (204 === jqXHR.status) {
                $('.chk-instance:checked', construct.div).each(function(index, element){
                    $(this).parents('.res-block').remove();
                })
            }
        });
    };

    function executeRequest(parameters, route, construct) {

        ClaroUtils.sendRequest(route, function(data) {
            //If there is a form, the submission handler above is used.
            //There is no handler otherwise.
            construct.divForm.empty().append(data).find('form').submit(function(e) {
                e.preventDefault();
                var action = construct.divForm.find('form').attr('action');
                action = action.replace('_instanceId', parameters.id)
                var id = construct.divForm.find('form').attr('id');
                ClaroUtils.sendForm(action, document.getElementById(id), function(xhr){
                    submissionHandler(xhr, parameters, construct);
                });
            });
            setLayout(construct);
        });
    };

    function submissionHandler(xhr, parameters, construct) {
        //If there is a json response, a node was returned.
        if (xhr.getResponseHeader('Content-Type') === 'application/json') {
            reload(construct);
            construct.divForm.empty();
            setLayout(construct);
        //If it's not a json response, we append the response at the top of the tree.
        } else {
            construct.divForm.empty().append(xhr.responseText).find('form').submit(function(e) {
                e.preventDefault();
                var action = construct.divForm.find('form').attr('action');
                //If it's a form, placeholders must be removed (the twig form doesn't know the instance parent,
                //that's why placeholders are used).'
                action = action.replace('_instanceId', parameters.id);
                action = action.replace('_resourceId', parameters.resourceId);
                var id = construct.divForm.find('form').attr('id');
                ClaroUtils.sendForm(action, document.getElementById(id), function(xhr){
                    submissionHandler(xhr, parameters, construct);
                });
            });
            setLayout(construct);
        }
    };

    function setLayout(construct) {
        if(construct.flatChkBox.is(':checked')){
            construct.pasteButton.attr('disabled', 'disabled');
        } else {
            construct.activePagerItem = 1;
            if($.isEmptyObject(construct.pasteIds) || $(".breadcrumb-link", construct.div).size() == 1){
                construct.pasteButton.attr('disabled', 'disabled');
            } else {
                construct.pasteButton.removeAttr('disabled');
            }
            if($.isEmptyObject(construct.selectedIds)){
                construct.deleteButton.attr('disabled', 'disabled');
                construct.downloadButton.attr('disabled', 'disabled');
            } else {
                 construct.deleteButton.removeAttr('disabled');
                 construct.downloadButton.removeAttr('disabled');
            }

            if($(".breadcrumb-link", construct.div).size() == 1) {
                construct.selectType.hide();
                construct.submitButton.hide();
            } else {
                construct.selectType.show();
                construct.submitButton.show();
            }

            if (construct.divForm.html()=='') {
                construct.closeButton.attr('disabled', 'disabled');
            } else {
                construct.closeButton.removeAttr('disabled');
            }
        }
    }

    function resizeBreadcrumb(construct){
        var resize = function(index, divSize) {
            var size = getBreadcrumbSize(construct);
            if(size > divSize && index >= 0) {
                var crumLink = (($(".breadcrumb-link", construct.div)).eq(index));
                formatResName(crumLink, 1, 9);
                index --;
                resize(index, divSize, construct);
            }
        }

        var getBreadcrumbSize = function(construct){
            var breadcrumbSize = 0;
            $(".breadcrumb-link", construct.div).each(function(index, element){
                breadcrumbSize += ($(this).width());
            })

            return breadcrumbSize;
        }

        var divSize = $('.breadcrumb', construct.div).width();
        var breadcrumbIndex = ($(".breadcrumb-link", construct.div)).size();

        resize(breadcrumbIndex, divSize, construct);
    }
})()