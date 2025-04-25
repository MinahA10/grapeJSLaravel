<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit {{ $model->editor_page_title }}</title>

    @foreach ($editorConfig->getStyles() as $style)
        <link rel="stylesheet" href="{{ $style }}">
    @endforeach

    <style>
        * {
            margin: 0;
            padding: 0;
        }

        .qvct-place {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
        }
    </style>
    <script>
        window.editorConfig = @json($editorConfig ?? []);

        Object.defineProperty(window, 'grapesjs', {
            value: {
                plugins: {
                    plugins: [],

                    /**
                     * Add new plugin. Plugins could not be overwritten
                     * @param {string} id Plugin ID
                     * @param {Function} plugin Function which contains all plugin logic
                     * @return {Function} The plugin function
                     * @example
                     * PluginManager.add('some-plugin', function(editor){
                     *   editor.Commands.add('new-command', {
                     *     run:  function(editor, senderBtn){
                     *       console.log('Executed new-command');
                     *     }
                     *   })
                     * });
                     */
                    add(id, plugin) {
                        if (this.plugins[id]) {
                            return this.plugins[id];
                        }

                        this.plugins[id] = plugin;

                        return plugin;
                    },

                    /**
                     * Returns plugin by ID
                     * @param  {string} id Plugin ID
                     * @return {Function|undefined} Plugin
                     * @example
                     * var plugin = PluginManager.get('some-plugin');
                     * plugin(editor);
                     */
                    get(id) {
                        return this.plugins[id];
                    },

                    /**
                     * Returns object with all plugins
                     * @return {Object}
                     */
                    getAll() {
                        return this.plugins;
                    },
                }
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const editor = grapesjs.init(window.editorConfig || {
                container: '#editor',
                height: '100vh',
                fromElement: false,
                storageManager: false,
            });
            editor.BlockManager.add('image-superposable', {
                label: 'Image superposable',
                category: 'Images',
                content: {
                    type: 'image',
                    style: {
                        width: '200px',
                        height: 'auto',
                        position: 'absolute',
                        top: '20px',
                        left: '20px',
                        zIndex: 1,
                    },
                    attributes: {
                        src: 'https://via.placeholder.com/200x150',
                    }
                }
            });
            editor.StyleManager.addProperty('extra', {
                name: 'Z-Index',
                property: 'z-index',
                type: 'integer',
                defaults: 1,
            });
        });
    </script>
</head>

<body>
    <div id="{{ str_replace('#', '', $editorConfig->container ?? 'editor') }}"></div>
    
    @foreach ($editorConfig->getScripts() as $script)
        <script src="{{ $script }}"></script>
    @endforeach
</body>
</html>