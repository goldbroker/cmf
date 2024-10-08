/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import Map from 'core-js/es6/map'
import '../jquery.cmf_context_menu'
import 'jquery.fancytree/modules/jquery.fancytree.js'
import 'jquery.fancytree/modules/jquery.fancytree.dnd.js'
import 'jquery.fancytree/skin-win8/ui.fancytree.min.css'
import '../../css/fontawesome-style.css'

var cache = new Map();

function getPropertyFromString(name, list) {
    var isOptional = name.substr(0, 1) === '?';
    var nameWithoutPrefix = (isOptional ? name.substr(1) : name);

    if (undefined === list[nameWithoutPrefix]) {
        if (isOptional) {
            return undefined;
        }

        throw 'Attribute "' + props[prop] + '" does not exists';
    }

    return list[nameWithoutPrefix];
}

/**
 * A tree browser adapter for the Fancytree library.
 *
 * @author Wouter J <wouter@wouterj.nl>
 * @see https://github.com/mar10/fancytree
 */
export class FancytreeAdapter {
    constructor(options) {
        if (!window.jQuery || !jQuery.fn.fancytree) {
            throw 'The FancytreeAdapter requires both jQuery and the FancyTree library.';
        }

        if (!options.request) {
            throw 'The FancytreeAdapter requires a request option.';
        }

        this.requestData = options.request;
        this.rootNode = options.root_node || '/';
        this.useCache = undefined === options.use_cache ? true : options.use_cache;
        this.boundToInput = false;
        this.sortableBy = undefined == options.sortableBy ? false : options.sortableBy;

        if (options.dnd && undefined == options.dnd.enabled) {
            options.dnd.enabled = true;
        }
        var alwaysTrueFunction = () => { return true };
        this.dndOptions = jQuery.extend({
            enabled: false,
            isNodeDraggable: alwaysTrueFunction,
            nodeAcceptsDraggable: alwaysTrueFunction
        }, options.dnd);

        if (this.dndOptions.enabled && !options.request.move) {
            throw 'The move request needs to be configured when drag \'n drop is enabled, pass it using the `request.move` option.';
        }
        this.dndOptions.request = options.request.move;

        // available actions (array)
        this.actions = new Array();
        // the Fancytree instance (FancytreeTree)
        this.tree = null;
        // the tree element (jQuery)
        this.$tree = null;
        // a map of path and related keys
        this.pathKeyMap = {};
    }

    bindToElement($elem) {
        if (this.$tree) {
            throw 'Cannot bind to multiple elements.';
        }

        if (!$elem instanceof jQuery) {
            throw  'FancytreeAdapter can only be adapted to a jQuery object.';
        }

        this.$tree = $elem;
        var actions = this.actions;
        var parseUrl = function (url, node) {
            if (typeof url == 'object' && url.hasOwnProperty('data')) {
                return getPropertyFromString(url.data, node.descriptors);
            }

            if (typeof url == 'function') {
                return url(requestNode);
            }

            if (typeof url == 'string') {
                return url;
            }
        };
        var requestNodeToFancytreeNode = (requestNode) => {
            if (requestNode.length === 0) {
                return;
            }

            if ('//' == requestNode.path || '/' == requestNode.path) {
                return requestNodeToFancytreeNode(requestNode.children[Object.keys(requestNode.children)[0]]);
            }

            var refPath = requestNode.path.replace('\/', '/').replace('//', '/');
            var key = this.pathKeyMap[refPath] || "" + jQuery.ui.fancytree._nextNodeKey++;
            var fancytreeNode = {
                title: requestNode.label,
                key: key,
                children: [],
                actions: {},
                refPath: refPath,
                type: requestNode.payload_type,
                unselectable: true
            };

            this.pathKeyMap[refPath] = key;

            if (requestNode.descriptors.hasOwnProperty('icon')) {
                fancytreeNode.icon = requestNode.descriptors.icon;
            }

            if(requestNode.descriptors.hasOwnProperty('position')) {
                fancytreeNode.position = requestNode.descriptors.position;
            }

            for (let actionName in actions) {
                var action = actions[actionName];
                var url = parseUrl(action.url, requestNode);

                if (url === undefined) {
                    continue;
                }
                fancytreeNode['actions'][actionName] = { label: actionName, iconClass: action.icon, url: url };
            }

            var childrenCount = 0;
            for (name in requestNode.children) {
                if (!requestNode.children.hasOwnProperty(name)) {
                    continue;
                }

                var child = requestNodeToFancytreeNode(requestNode.children[name]);
                if (child) {
                    fancytreeNode.children.push(child);
                }
                childrenCount++;
            }

            if (0 != childrenCount) {
                fancytreeNode.folder = true;
                fancytreeNode.lazy = true;

                if (0 === fancytreeNode.children.length) {
                    fancytreeNode.children = null;
                }
            }

            return fancytreeNode;
        };

        var requestData = this.requestData;
        var useCache = this.useCache;
        var fancytreeOptions = {
            // the start data (root node + children)
            source: (useCache && cache.has(this.rootNode)) ? cache.get(this.rootNode) : requestData.load(this.rootNode),

            // lazy load the children when a node is collapsed
            lazyLoad: function (event, data) {
                var path = data.node.data.refPath;
                if (useCache && cache.has(path)) {
                    data.result = cache.get(path);
                } else {
                    var loadData = requestData.load(path);

                    if (Array.isArray(loadData)) {
                        data.result = loadData;
                    } else {
                        data.result = jQuery.extend({
                            data: {}
                        }, loadData);
                    }
                }
            },

            // transform the JSON response into a data structure that's supported by FancyTree
            postProcess: function (event, data) {
                if (data.hasOwnProperty('error') && null != data.error) {
                    data.result = {
                        // todo: maybe use a more admin friendly error message in prod?
                        error: 'An error occured while retrieving the nodes: ' + data.error
                    };

                    return;
                }

                let result = requestNodeToFancytreeNode(data.response);
                let nodeIsDuplicate = function (node, parentPath) {
                    return parentPath == node.refPath;
                };

                if (nodeIsDuplicate(result, data.node.data.refPath)) {
                    result = result.children;
                } else {
                    result = [result];
                }

                if (result.length == 1 && undefined !== result[0].folder) {
                    result[0].expanded = true;
                }

                data.result = result;
                if (useCache) {
                    cache.set(data.node.data.refPath, result);
                }
            },

            // always show the active node
            activeVisible: true
        };

        if (this.sortableBy) {
            fancytreeOptions.sortChildren = (a, b) => {
                var current = a.data[this.sortableBy];
                var next = b.data[this.sortableBy];
                if (current == next) {
                    return 0;
                } else if (current < next) {
                    return -1;
                } else  {
                    return 1;
                }
            };
        }

        if (this.dndOptions.enabled) {
            fancytreeOptions.extensions = ['dnd'];
            fancytreeOptions.dnd = {
                dragStart: (node, data) => {
                    return this.dndOptions.isNodeDraggable(node, data);
                },
                dragEnter: (node, data) => {
                    return this.dndOptions.nodeAcceptsDraggable(node, data);
                },
                dragExpand: (node, data) => {
                    return true;
                },
                dragDrop: (node, data) => {
                    let dropedNode = data.otherNode;
                    let dropedAtNode = data.node;

                    let dropNodePath = dropedNode.data.refPath;
                    let dropedAtPath = dropedAtNode.data.refPath;
                    let positionBefore = 'over' != data.hitMode && 'child' != data.hitMode;
                    let parentNode = positionBefore ? dropedAtNode.parent : dropedAtNode;
                    let parenPath = parentNode.data.refPath;
                    let targetPath = parenPath + '/' + dropNodePath.substr(1 + dropNodePath.lastIndexOf('/'));

                    let formerIcon = dropedNode.icon;

                    let moveNodeInTree = (responseData) => {
                        dropedNode.remove();
                        if (positionBefore) {
                            parentNode.children.forEach((node) => {
                            if (node.data.position >= responseData.descriptors.position) {
                                node.data.position++;
                            }
                        });
                      }
                        parentNode.addChildren(requestNodeToFancytreeNode(responseData));
                    };

                    let setIcon = (nodeToSetOn, icon) => {
                        nodeToSetOn.icon = icon;
                        dropedNode.renderTitle();
                    };

                    setIcon(dropedNode, 'fa fa-spinner fa-spin')

                    let onError = (jqxhr) => {
                        let message = 'Failed to move node';
                        let formerLabel = dropedNode.title;
                        if (jqxhr.hasOwnProperty('responseJSON') && jqxhr.responseJSON.hasOwnProperty('message')) {
                            message += ': ' + jqxhr.responseJSON.message;
                        }
                        let details = null;
                        if (jqxhr.hasOwnProperty('responseJSON')) {
                            details = jqxhr.responseJSON;
                        }
                        dropedNode._error = { message: message, details: details};
                        dropedNode.renderStatus();

                        dropedNode.title +=  '[' + message + ']';
                        dropedNode.renderTitle();

                        console.error(message);
                        setIcon(dropedNode, formerIcon);

                        setTimeout(function () {
                            dropedNode._error = null;
                            dropedNode.title = formerLabel;
                            dropedNode.renderTitle();
                            dropedNode.renderStatus();
                        }, 1000);
                    };
                    this.requestData.move(dropNodePath, targetPath).done((responseData) => {
                        if (positionBefore && this.dndOptions.reorder) {
                            this.requestData.reorder(parenPath, dropedAtPath, targetPath, data.hitMode).done((responseData) => {
                                moveNodeInTree(responseData);
                                if (fancytreeOptions.hasOwnProperty('sortChildren')) {
                                    parentNode.sortChildren(fancytreeOptions.sortChildren, true);
                                }
                                setIcon(dropedNode, formerIcon);
                          }).fail( (jqxhr) => {
                              onError(jqxhr);
                              setTimeout(() => {
                                  this.requestData.move(targetPath, dropNodePath).done((responseData) => {
                                      if (fancytreeOptions.hasOwnProperty('sortChildren')) {
                                         parentNode.sortChildren(fancytreeOptions.sortChildren, true);
                                      }
                                      setIcon(dropedNode, formerIcon);
                                  });
                              }, 1000);

                            });
                        } else {
                            dropedNode.icon = formerIcon;
                            moveNodeInTree(responseData);
                        }
                    }).fail(onError);
                }
            };
        }

        this.$tree.fancytree(fancytreeOptions);

        if (this.actions) {
            this.$tree.cmfContextMenu({
                delegate: 'span.fancytree-title',
                wrapperTemplate: '<ul class="dropdown-menu" style="display:block;"></ul>',
                actionTemplate: '<li role="presentation"><a role="menuitem" href="{{ url }}"><i class="{{ iconClass }}"></i> {{ label }}</li>',
                actions: function ($node) {
                    return jQuery.ui.fancytree.getNode($node).data.actions;
                }
            });
        }

        this.tree = $.ui.fancytree.getTree(this.$tree);

        this.tree.getNodeByRefPath = function (refPath) {
            return this.findFirst((node) => {
                return node.data.refPath == refPath;
            });
        };

        // We do not want to do anything on activation atm.
        this.$tree.fancytree('option', 'activate', (event, data) => {
            if (!this.boundToInput) {
                data.node.setActive(false);
                data.node.setFocus(false);
            }
        });
    }

    bindToInput($input) {
        this.boundToInput = true;

        // output active node to input field
        this.$tree.fancytree('option', 'activate', (event, data) => {
            $input.val(data.node.data.refPath);
        });

        var showPath = (path) => {
            if (!this.pathKeyMap.hasOwnProperty(path)) {
                var parts = path.split('/');

                while (!this.pathKeyMap.hasOwnProperty(parts.join('/')) && parts.pop());

                if (parts.length === 0) {
                    return;
                }

                var loadedPath = parts.join('/');
                var pathsToLoad = path.substr(loadedPath.length + 1).split('/');

                pathsToLoad.forEach((pathToLoad) => {
                    this.pathKeyMap[loadedPath += '/' + pathToLoad] = "" + jQuery.ui.fancytree._nextNodeKey++;
                });
            }

            this.tree.loadKeyPath(generateKeyPath(path), function (node, status) {
                if ('ok' == status) {
                    node.setExpanded();
                    node.setActive();
                }
            });
        };
        var generateKeyPath = (path) => {
            var keyPath = '';
            var refPath = '';
            var subPaths = path.split('/');

            subPaths.forEach((subPath) => {
                if (subPath == '' || !this.pathKeyMap.hasOwnProperty(refPath += '/' + subPath)) {
                    return;
                }

                keyPath += '/' + this.pathKeyMap[refPath];
            });

            return keyPath;
        };

        // use initial input value as active node
        this.$tree.bind('fancytreeinit', function (event, data) {
            showPath($input.val());
        });

        // change active node when the value of the input field changed
        $input.on('change', function (e) {
            showPath($(this).val());
        });
    }

    addAction(name, url, icon) {
        this.actions[name] = { url: url, icon: icon };
    }

    static _resetCache() {
        cache.clear();
    }
}
