<template>
  <div class="content-editor border border-gray-200 dark:border-dark-600 rounded-xl overflow-hidden">
    <!-- Mode Tabs -->
    <div class="flex items-center justify-between px-3 py-2 border-b border-gray-200 dark:border-dark-600 bg-gray-50 dark:bg-dark-700/30">
      <div class="flex items-center gap-1 bg-gray-100 dark:bg-dark-600 rounded-lg p-0.5">
        <button type="button" @click="switchMode('richtext')"
          :class="mode === 'richtext' ? 'bg-white dark:bg-dark-800 text-brand-violet dark:text-brand-cyan shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
          class="px-3 py-1 text-xs font-semibold rounded-md transition-all">
          Rich Text
        </button>
        <button type="button" @click="switchMode('markdown')"
          :class="mode === 'markdown' ? 'bg-white dark:bg-dark-800 text-brand-violet dark:text-brand-cyan shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
          class="px-3 py-1 text-xs font-semibold rounded-md transition-all">
          Markdown
        </button>
      </div>
      <div class="flex items-center gap-2">
        <span class="text-[10px] text-gray-400">{{ charCount }} chars</span>
        <button v-if="mode === 'markdown'" type="button" @click="showPreview = !showPreview"
          :class="showPreview ? 'text-brand-violet dark:text-brand-cyan bg-brand-violet/10 dark:bg-brand-cyan/10' : 'text-gray-400 hover:text-gray-600'"
          class="p-1.5 rounded-lg transition-colors" title="Toggle Preview">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        </button>
      </div>
    </div>

    <!-- ═══════ Rich Text (Tiptap) Editor ═══════ -->
    <div v-show="mode === 'richtext'">
      <!-- Toolbar -->
      <div v-if="editor" class="tiptap-toolbar flex flex-wrap items-center gap-0.5 px-3 py-2 border-b border-gray-200 dark:border-dark-600 bg-gray-50/80 dark:bg-dark-700/20">
        <!-- Text formatting -->
        <ToolBtn icon="bold" :active="editor.isActive('bold')" @click="editor.chain().focus().toggleBold().run()" title="Bold" />
        <ToolBtn icon="italic" :active="editor.isActive('italic')" @click="editor.chain().focus().toggleItalic().run()" title="Italic" />
        <ToolBtn icon="underline" :active="editor.isActive('underline')" @click="editor.chain().focus().toggleUnderline().run()" title="Underline" />
        <ToolBtn icon="strike" :active="editor.isActive('strike')" @click="editor.chain().focus().toggleStrike().run()" title="Strikethrough" />
        <ToolBtn icon="highlight" :active="editor.isActive('highlight')" @click="editor.chain().focus().toggleHighlight().run()" title="Highlight" />
        <ToolBtn icon="subscript" :active="editor.isActive('subscript')" @click="editor.chain().focus().toggleSubscript().run()" title="Subscript" />
        <ToolBtn icon="superscript" :active="editor.isActive('superscript')" @click="editor.chain().focus().toggleSuperscript().run()" title="Superscript" />

        <div class="w-px h-5 bg-gray-300 dark:bg-dark-500 mx-1" />

        <!-- Headings -->
        <ToolBtn icon="h1" :active="editor.isActive('heading', { level: 1 })" @click="editor.chain().focus().toggleHeading({ level: 1 }).run()" title="Heading 1" />
        <ToolBtn icon="h2" :active="editor.isActive('heading', { level: 2 })" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" title="Heading 2" />
        <ToolBtn icon="h3" :active="editor.isActive('heading', { level: 3 })" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" title="Heading 3" />

        <div class="w-px h-5 bg-gray-300 dark:bg-dark-500 mx-1" />

        <!-- Lists -->
        <ToolBtn icon="ul" :active="editor.isActive('bulletList')" @click="editor.chain().focus().toggleBulletList().run()" title="Bullet List" />
        <ToolBtn icon="ol" :active="editor.isActive('orderedList')" @click="editor.chain().focus().toggleOrderedList().run()" title="Ordered List" />
        <ToolBtn icon="blockquote" :active="editor.isActive('blockquote')" @click="editor.chain().focus().toggleBlockquote().run()" title="Blockquote" />
        <ToolBtn icon="hr" @click="editor.chain().focus().setHorizontalRule().run()" title="Horizontal Rule" />

        <div class="w-px h-5 bg-gray-300 dark:bg-dark-500 mx-1" />

        <!-- Code -->
        <ToolBtn icon="code" :active="editor.isActive('code')" @click="editor.chain().focus().toggleCode().run()" title="Inline Code" />
        <div class="relative">
          <ToolBtn icon="codeblock" :active="editor.isActive('codeBlock')" @click="showLangMenu = !showLangMenu" title="Code Block" />
          <div v-if="showLangMenu" class="absolute top-full left-0 mt-1 z-50 w-44 max-h-56 overflow-y-auto bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl shadow-xl py-1 scrollbar-thin">
            <button v-for="lang in languages" :key="lang.value" type="button"
              @click="insertCodeBlock(lang.value)"
              class="w-full text-left px-3 py-1.5 text-xs hover:bg-brand-violet/10 dark:hover:bg-brand-cyan/10 text-gray-700 dark:text-gray-300 hover:text-brand-violet dark:hover:text-brand-cyan transition-colors">
              {{ lang.label }}
            </button>
          </div>
        </div>

        <div class="w-px h-5 bg-gray-300 dark:bg-dark-500 mx-1" />

        <!-- Links & Media -->
        <ToolBtn icon="link" :active="editor.isActive('link')" @click="setLink" title="Insert Link" />
        <ToolBtn icon="image" @click="addImage" :disabled="uploading" title="Upload Image" />
        <ToolBtn icon="video" @click="addVideo" title="Embed Video (YouTube, etc.)" />
        <span v-if="uploading" class="text-[10px] text-brand-violet dark:text-brand-cyan animate-pulse ml-1">Uploading...</span>

        <div class="w-px h-5 bg-gray-300 dark:bg-dark-500 mx-1" />

        <!-- Table -->
        <ToolBtn icon="table" @click="editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()" title="Insert Table" />
        <template v-if="editor.isActive('table')">
          <ToolBtn icon="addcol" @click="editor.chain().focus().addColumnAfter().run()" title="Add Column" />
          <ToolBtn icon="addrow" @click="editor.chain().focus().addRowAfter().run()" title="Add Row" />
          <ToolBtn icon="delcol" @click="editor.chain().focus().deleteColumn().run()" title="Delete Column" />
          <ToolBtn icon="delrow" @click="editor.chain().focus().deleteRow().run()" title="Delete Row" />
          <ToolBtn icon="deltable" @click="editor.chain().focus().deleteTable().run()" title="Delete Table" />
        </template>

        <div class="w-px h-5 bg-gray-300 dark:bg-dark-500 mx-1" />

        <!-- Text alignment -->
        <ToolBtn icon="alignleft" :active="editor.isActive({ textAlign: 'left' })" @click="editor.chain().focus().setTextAlign('left').run()" title="Align Left" />
        <ToolBtn icon="aligncenter" :active="editor.isActive({ textAlign: 'center' })" @click="editor.chain().focus().setTextAlign('center').run()" title="Align Center" />
        <ToolBtn icon="alignright" :active="editor.isActive({ textAlign: 'right' })" @click="editor.chain().focus().setTextAlign('right').run()" title="Align Right" />

        <div class="flex-1" />

        <!-- Undo/Redo -->
        <ToolBtn icon="undo" @click="editor.chain().focus().undo().run()" :disabled="!editor.can().undo()" title="Undo" />
        <ToolBtn icon="redo" @click="editor.chain().focus().redo().run()" :disabled="!editor.can().redo()" title="Redo" />
        <ToolBtn icon="clear" @click="editor.chain().focus().clearNodes().unsetAllMarks().run()" title="Clear Formatting" />
      </div>

      <!-- Editor content -->
      <EditorContent :editor="editor" class="tiptap-content" />
    </div>

    <!-- ═══════ Markdown Editor ═══════ -->
    <div v-show="mode === 'markdown'">
      <div class="flex items-center gap-1 px-3 py-2 border-b border-gray-200 dark:border-dark-600 bg-gray-50/50 dark:bg-dark-700/20 flex-wrap">
        <button type="button" @click="insertMd('**', '**')" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors" title="Bold"><span class="text-xs font-bold text-gray-500">B</span></button>
        <button type="button" @click="insertMd('*', '*')" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors" title="Italic"><span class="text-xs italic text-gray-500">I</span></button>
        <button type="button" @click="insertMd('~~', '~~')" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors" title="Strikethrough"><span class="text-xs line-through text-gray-500">S</span></button>
        <button type="button" @click="insertMd('`', '`')" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors" title="Inline Code">
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
        </button>
        <div class="w-px h-4 bg-gray-300 dark:bg-dark-500 mx-1" />
        <button type="button" @click="insertMd('# ', '')" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors" title="Heading 1"><span class="text-xs font-bold text-gray-500">H1</span></button>
        <button type="button" @click="insertMd('## ', '')" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors" title="Heading 2"><span class="text-xs font-bold text-gray-500">H2</span></button>
        <button type="button" @click="insertMd('### ', '')" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors" title="Heading 3"><span class="text-xs font-bold text-gray-500">H3</span></button>
        <div class="w-px h-4 bg-gray-300 dark:bg-dark-500 mx-1" />
        <button type="button" @click="insertMd('[', '](url)')" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors" title="Link">
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
        </button>
        <button type="button" @click="insertMd('![alt](', ')')" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors" title="Image">
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </button>
        <button type="button" @click="insertMd('- ', '')" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors" title="Bullet List">
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <button type="button" @click="insertMd('> ', '')" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors" title="Blockquote">
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        </button>
        <div class="w-px h-4 bg-gray-300 dark:bg-dark-500 mx-1" />
        <button type="button" @click="insertMd('---\n', '')" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors" title="Horizontal Rule"><span class="text-xs text-gray-500">HR</span></button>
        <button type="button" @click="insertMd('| Col 1 | Col 2 | Col 3 |\n| --- | --- | --- |\n| ', ' | | |\n')" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors" title="Table"><span class="text-xs text-gray-500">TBL</span></button>
        <div class="w-px h-4 bg-gray-300 dark:bg-dark-500 mx-1" />
        <!-- Code block with language picker -->
        <div class="relative">
          <button type="button" @click="showMdLangMenu = !showMdLangMenu" class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors" title="Code Block">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          </button>
          <div v-if="showMdLangMenu" class="absolute top-full left-0 mt-1 z-50 w-44 max-h-56 overflow-y-auto bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl shadow-xl py-1 scrollbar-thin">
            <button v-for="lang in languages" :key="lang.value" type="button"
              @click="insertMd('```' + lang.value + '\n', '\n```'); showMdLangMenu = false"
              class="w-full text-left px-3 py-1.5 text-xs hover:bg-brand-violet/10 dark:hover:bg-brand-cyan/10 text-gray-700 dark:text-gray-300 hover:text-brand-violet dark:hover:text-brand-cyan transition-colors">
              {{ lang.label }}
            </button>
          </div>
        </div>
      </div>

      <!-- Markdown textarea -->
      <div class="flex" :class="showPreview ? 'divide-x divide-gray-200 dark:divide-dark-600' : ''">
        <textarea
          ref="mdTextarea"
          :value="modelValue"
          @input="$emit('update:modelValue', $event.target.value)"
          rows="20"
          class="flex-1 w-full px-4 py-3 bg-transparent text-sm font-sans dark:text-gray-200 placeholder-gray-400 focus:outline-none resize-none"
          :class="showPreview ? 'w-1/2' : 'w-full'"
          placeholder="Write your content in Markdown..."
          spellcheck="false"
        />
        <div v-if="showPreview" class="w-1/2 px-4 py-3 overflow-y-auto prose prose-sm dark:prose-invert max-w-none max-h-[500px]" v-html="renderedPreview" />
      </div>
    </div>

    <!-- Footer -->
    <div class="flex items-center justify-between px-3 py-2 border-t border-gray-200 dark:border-dark-600 bg-gray-50 dark:bg-dark-700/30">
      <span class="text-[10px] text-gray-400">
        {{ mode === 'richtext' ? 'Rich text editor • Content saved as Markdown' : 'Markdown mode • Syntax highlighting supported' }}
      </span>
      <span class="text-[10px] text-gray-400">
        Tip: Use ``` + language name for code blocks
      </span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onBeforeUnmount, nextTick, defineComponent, h } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import CodeBlockLowlight from '@tiptap/extension-code-block-lowlight'
import Image from '@tiptap/extension-image'
import Link from '@tiptap/extension-link'
import Underline from '@tiptap/extension-underline'
import Placeholder from '@tiptap/extension-placeholder'
import TextAlign from '@tiptap/extension-text-align'
import { Highlight } from '@tiptap/extension-highlight'
import { TextStyle } from '@tiptap/extension-text-style'
import { Table } from '@tiptap/extension-table'
import TableRow from '@tiptap/extension-table-row'
import TableCell from '@tiptap/extension-table-cell'
import TableHeader from '@tiptap/extension-table-header'
import HorizontalRule from '@tiptap/extension-horizontal-rule'
import Subscript from '@tiptap/extension-subscript'
import Superscript from '@tiptap/extension-superscript'
import Youtube from '@tiptap/extension-youtube'
import { common, createLowlight } from 'lowlight'
import { marked } from 'marked'
import DOMPurify from 'dompurify'
import { adminApi } from '@/services/api'

const lowlight = createLowlight(common)

const purifyConfig = {
  ADD_TAGS: ['iframe'],
  ADD_ATTR: ['allow', 'allowfullscreen', 'frameborder', 'src', 'style', 'data-youtube-video'],
}

const props = defineProps({
  modelValue: { type: String, default: '' }
})
const emit = defineEmits(['update:modelValue'])

const mode = ref('richtext')
const showPreview = ref(false)
const mdTextarea = ref(null)
const showLangMenu = ref(false)
const showMdLangMenu = ref(false)
const imageInput = ref(null)
const uploading = ref(false)
let updatingFromProp = false

const languages = [
  { label: 'Plain Text', value: '' },
  { label: 'JavaScript', value: 'javascript' },
  { label: 'TypeScript', value: 'typescript' },
  { label: 'PHP', value: 'php' },
  { label: 'Python', value: 'python' },
  { label: 'Java', value: 'java' },
  { label: 'C / C++', value: 'cpp' },
  { label: 'C#', value: 'csharp' },
  { label: 'Go', value: 'go' },
  { label: 'Rust', value: 'rust' },
  { label: 'Ruby', value: 'ruby' },
  { label: 'Swift', value: 'swift' },
  { label: 'Kotlin', value: 'kotlin' },
  { label: 'HTML', value: 'xml' },
  { label: 'CSS', value: 'css' },
  { label: 'SCSS', value: 'scss' },
  { label: 'JSON', value: 'json' },
  { label: 'YAML', value: 'yaml' },
  { label: 'SQL', value: 'sql' },
  { label: 'Bash / Shell', value: 'bash' },
  { label: 'Docker', value: 'dockerfile' },
  { label: 'Markdown', value: 'markdown' },
]

// Wrap standalone YouTube iframes with data-youtube-video div for TipTap recognition
function wrapYoutubeIframes(html) {
  return html.replace(
    /<iframe\s([^>]*src="[^"]*(?:youtube\.com|youtu\.be)[^"]*"[^>]*)><\/iframe>/gi,
    (match, attrs) => `<div data-youtube-video><iframe ${attrs}></iframe></div>`
  )
}

function sanitizeHtml(md) {
  const raw = typeof md === 'string' ? marked(md) : ''
  const clean = DOMPurify.sanitize(raw, purifyConfig)
  return wrapYoutubeIframes(clean)
}

// ─── Tiptap Editor ───
const editor = useEditor({
  content: props.modelValue ? sanitizeHtml(props.modelValue) : '',
  extensions: [
    StarterKit.configure({ codeBlock: false, horizontalRule: false }),
    CodeBlockLowlight.configure({ lowlight, defaultLanguage: 'plaintext' }),
    Image.configure({ inline: false, allowBase64: true }),
    Link.configure({ openOnClick: false, autolink: true }),
    Underline,
    Placeholder.configure({ placeholder: 'Write your article content...' }),
    TextAlign.configure({ types: ['heading', 'paragraph'] }),
    Highlight.configure({ multicolor: false }),
    TextStyle,
    Table.configure({ resizable: true }),
    TableRow,
    TableCell,
    TableHeader,
    HorizontalRule,
    Subscript,
    Superscript,
    Youtube.configure({
      inline: false,
      ccLanguage: 'en',
      interfaceLanguage: 'en',
      allowFullscreen: true,
      autoplay: false,
    }),
  ],
  onUpdate({ editor: ed }) {
    if (updatingFromProp) return
    const html = ed.getHTML()
    const md = htmlToMarkdown(html)
    emit('update:modelValue', md)
  },
})

const charCount = computed(() => (props.modelValue || '').length)
const renderedPreview = computed(() => {
  if (!props.modelValue) return '<p class="text-gray-400">Preview will appear here...</p>'
  return sanitizeHtml(props.modelValue)
})

// Close lang menu on click outside
function closeLangMenus(e) {
  if (showLangMenu.value || showMdLangMenu.value) {
    showLangMenu.value = false
    showMdLangMenu.value = false
  }
}

// ─── HTML → Markdown converter ───
function htmlToMarkdown(html) {
  if (!html || html === '<p></p>') return ''
  const div = document.createElement('div')
  div.innerHTML = html
  return convertNode(div).trim()
}

function convertNode(node) {
  let result = ''
  for (const child of node.childNodes) {
    if (child.nodeType === 3) {
      result += child.textContent
    } else if (child.nodeType === 1) {
      const tag = child.tagName.toLowerCase()
      const inner = convertNode(child)
      switch (tag) {
        case 'h1': result += `# ${inner}\n\n`; break
        case 'h2': result += `## ${inner}\n\n`; break
        case 'h3': result += `### ${inner}\n\n`; break
        case 'h4': result += `#### ${inner}\n\n`; break
        case 'p': result += `${inner}\n\n`; break
        case 'br': result += '\n'; break
        case 'strong': case 'b': result += `**${inner}**`; break
        case 'em': case 'i': result += `*${inner}*`; break
        case 's': case 'del': result += `~~${inner}~~`; break
        case 'u': result += `<u>${inner}</u>`; break
        case 'sub': result += `<sub>${inner}</sub>`; break
        case 'sup': result += `<sup>${inner}</sup>`; break
        case 'mark': result += `==${inner}==`; break
        case 'a': result += `[${inner}](${child.getAttribute('href') || ''})`; break
        case 'img': result += `![${child.getAttribute('alt') || ''}](${child.getAttribute('src') || ''})\n\n`; break
        case 'iframe': {
          const src = child.getAttribute('src') || ''
          if (src.match(/youtube\.com|youtu\.be/i)) {
            result += `<div data-youtube-video><iframe src="${src}" frameborder="0" allowfullscreen style="width:100%;aspect-ratio:16/9;"></iframe></div>\n\n`
          } else {
            result += `<iframe src="${src}" frameborder="0" allowfullscreen style="width:100%;aspect-ratio:16/9;"></iframe>\n\n`
          }
          break
        }
        case 'div': {
          if (child.getAttribute('data-youtube-video') !== null) {
            const iframe = child.querySelector('iframe')
            if (iframe) {
              const src = iframe.getAttribute('src') || ''
              result += `<div data-youtube-video><iframe src="${src}" frameborder="0" allowfullscreen style="width:100%;aspect-ratio:16/9;"></iframe></div>\n\n`
            } else {
              result += inner
            }
          } else {
            result += inner
          }
          break
        }
        case 'blockquote': result += inner.split('\n').filter(l => l.trim()).map(l => `> ${l}`).join('\n') + '\n\n'; break
        case 'hr': result += '---\n\n'; break
        case 'pre': {
          const code = child.querySelector('code')
          const langClass = code?.className?.match(/language-(\w+)/)?.[1] || ''
          const text = code ? code.textContent : child.textContent
          result += '```' + langClass + '\n' + text + '\n```\n\n'
          break
        }
        case 'code': result += `\`${inner}\``; break
        case 'ul': {
          for (const li of child.querySelectorAll(':scope > li')) {
            result += `- ${convertNode(li).trim()}\n`
          }
          result += '\n'
          break
        }
        case 'ol': {
          let i = 1
          for (const li of child.querySelectorAll(':scope > li')) {
            result += `${i++}. ${convertNode(li).trim()}\n`
          }
          result += '\n'
          break
        }
        case 'li': result += inner; break
        case 'table': {
          const rows = child.querySelectorAll('tr')
          const tableRows = []
          rows.forEach((row, ri) => {
            const cells = row.querySelectorAll('th, td')
            const rowData = []
            cells.forEach(cell => rowData.push(convertNode(cell).trim()))
            tableRows.push('| ' + rowData.join(' | ') + ' |')
            if (ri === 0) {
              tableRows.push('| ' + rowData.map(() => '---').join(' | ') + ' |')
            }
          })
          result += tableRows.join('\n') + '\n\n'
          break
        }
        case 'thead': case 'tbody': case 'tfoot': case 'tr':
        case 'th': case 'td': case 'colgroup': case 'col':
          result += inner; break
        default: result += inner
      }
    }
  }
  return result
}

function insertCodeBlock(lang) {
  showLangMenu.value = false
  editor.value?.chain().focus().setCodeBlock({ language: lang }).run()
}

function setLink() {
  const prev = editor.value?.getAttributes('link').href
  const url = window.prompt('Enter URL:', prev || 'https://')
  if (url === null) return
  if (url === '') {
    editor.value?.chain().focus().extendMarkRange('link').unsetLink().run()
  } else {
    editor.value?.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
  }
}

function addImage() {
  // Create a hidden file input and trigger it
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = 'image/jpeg,image/png,image/webp,image/gif'
  input.onchange = async () => {
    const file = input.files?.[0]
    if (!file) return
    uploading.value = true
    try {
      const formData = new FormData()
      formData.append('file', file)
      const { data } = await adminApi.uploadMedia(formData)
      const url = data.data?.url
      if (url) {
        editor.value?.chain().focus().setImage({ src: url, alt: file.name }).run()
      }
    } catch (err) {
      alert('Image upload failed: ' + (err.response?.data?.message || err.message))
    } finally {
      uploading.value = false
    }
  }
  input.click()
}

function addVideo() {
  const url = window.prompt('Paste video URL (YouTube, etc.):')
  if (!url) return
  // Try YouTube first via the extension
  if (url.match(/youtube\.com|youtu\.be/i)) {
    editor.value?.chain().focus().setYoutubeVideo({ src: url }).run()
  } else {
    // For other platforms (Facebook, Vimeo, etc.), insert as iframe HTML
    const iframe = `<div data-video-embed="true"><iframe src="${url}" frameborder="0" allowfullscreen style="width:100%;aspect-ratio:16/9;"></iframe></div>`
    editor.value?.chain().focus().insertContent(iframe).run()
  }
}

function switchMode(newMode) {
  if (newMode === mode.value) return
  if (newMode === 'richtext' && editor.value) {
    updatingFromProp = true
    const html = sanitizeHtml(props.modelValue || '')
    editor.value.commands.setContent(html, false)
    nextTick(() => { updatingFromProp = false })
  }
  mode.value = newMode
}

function insertMd(before, after) {
  const el = mdTextarea.value
  if (!el) return
  const start = el.selectionStart
  const end = el.selectionEnd
  const text = props.modelValue || ''
  const selected = text.substring(start, end) || 'text'
  const newText = text.substring(0, start) + before + selected + after + text.substring(end)
  emit('update:modelValue', newText)
  nextTick(() => {
    el.focus()
    el.selectionStart = start + before.length
    el.selectionEnd = start + before.length + selected.length
  })
}

onBeforeUnmount(() => {
  editor.value?.destroy()
})

// ─── Toolbar Button Component ───
const ToolBtn = defineComponent({
  props: {
    icon: String,
    active: Boolean,
    disabled: Boolean,
    title: String,
  },
  emits: ['click'],
  setup(props, { emit }) {
    const icons = {
      bold: () => h('span', { class: 'text-xs font-extrabold', innerHTML: 'B' }),
      italic: () => h('span', { class: 'text-xs font-semibold italic', innerHTML: 'I' }),
      underline: () => h('span', { class: 'text-xs font-semibold underline', innerHTML: 'U' }),
      strike: () => h('span', { class: 'text-xs font-semibold line-through', innerHTML: 'S' }),
      highlight: () => h('span', { class: 'text-xs font-semibold bg-yellow-200 dark:bg-yellow-600 px-0.5 rounded', innerHTML: 'H' }),
      subscript: () => h('span', { class: 'text-[10px] font-semibold', innerHTML: 'X<sub class="text-[8px]">2</sub>' }),
      superscript: () => h('span', { class: 'text-[10px] font-semibold', innerHTML: 'X<sup class="text-[8px]">2</sup>' }),
      h1: () => h('span', { class: 'text-xs font-bold', innerHTML: 'H<sub class="text-[8px]">1</sub>' }),
      h2: () => h('span', { class: 'text-xs font-bold', innerHTML: 'H<sub class="text-[8px]">2</sub>' }),
      h3: () => h('span', { class: 'text-xs font-bold', innerHTML: 'H<sub class="text-[8px]">3</sub>' }),
      ul: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', innerHTML: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>' }),
      ol: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', innerHTML: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>' }),
      blockquote: () => h('span', { class: 'text-base font-serif font-bold leading-none', innerHTML: '&#8220;' }),
      hr: () => h('span', { class: 'text-xs text-gray-400 font-mono', innerHTML: '—' }),
      code: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', innerHTML: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>' }),
      codeblock: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', innerHTML: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>' }),
      link: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', innerHTML: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>' }),
      image: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', innerHTML: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>' }),
      video: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', innerHTML: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>' }),
      table: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', innerHTML: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 3v18M14 3v18M3 6a3 3 0 013-3h12a3 3 0 013 3v12a3 3 0 01-3 3H6a3 3 0 01-3-3V6z"/>' }),
      addcol: () => h('span', { class: 'text-[9px] font-bold', innerHTML: '+C' }),
      addrow: () => h('span', { class: 'text-[9px] font-bold', innerHTML: '+R' }),
      delcol: () => h('span', { class: 'text-[9px] font-bold text-red-400', innerHTML: '-C' }),
      delrow: () => h('span', { class: 'text-[9px] font-bold text-red-400', innerHTML: '-R' }),
      deltable: () => h('span', { class: 'text-[9px] font-bold text-red-400', innerHTML: '×T' }),
      alignleft: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', innerHTML: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M3 12h12M3 18h18"/>' }),
      aligncenter: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', innerHTML: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M6 12h12M3 18h18"/>' }),
      alignright: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', innerHTML: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M9 12h12M3 18h18"/>' }),
      undo: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', innerHTML: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l4-4m-4 4l4 4"/>' }),
      redo: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', innerHTML: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a5 5 0 00-5 5v2m15-7l-4-4m4 4l-4 4"/>' }),
      clear: () => h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', innerHTML: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>' }),
    }
    return () => h('button', {
      type: 'button',
      class: [
        'p-1.5 rounded-lg transition-all duration-150 flex items-center justify-center min-w-[28px] h-7',
        props.active
          ? 'bg-brand-violet/15 text-brand-violet dark:bg-brand-cyan/15 dark:text-brand-cyan'
          : 'text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-dark-600 hover:text-gray-700 dark:hover:text-gray-200',
        props.disabled ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer',
      ],
      title: props.title,
      disabled: props.disabled,
      onClick: (e) => { e.preventDefault(); if (!props.disabled) emit('click') },
    }, [icons[props.icon]?.()])
  },
})
</script>

<style>
/* ─── Tiptap Editor Content ─── */
.tiptap-content .tiptap {
  min-height: 420px;
  padding: 16px 20px;
  font-size: 0.9rem;
  line-height: 1.8;
  outline: none;
}
.tiptap-content .tiptap:focus {
  outline: none;
}
.dark .tiptap-content .tiptap {
  color: #e5e7eb;
}

/* Placeholder */
.tiptap-content .tiptap p.is-editor-empty:first-child::before {
  content: attr(data-placeholder);
  float: left;
  color: #9ca3af;
  pointer-events: none;
  height: 0;
}
.dark .tiptap-content .tiptap p.is-editor-empty:first-child::before {
  color: #6b7280;
}

/* Headings */
.tiptap-content .tiptap h1 { font-size: 2em; font-weight: 700; margin: 1em 0 0.5em; font-family: 'Fira Code', monospace; }
.tiptap-content .tiptap h2 { font-size: 1.5em; font-weight: 700; margin: 1em 0 0.4em; font-family: 'Fira Code', monospace; }
.tiptap-content .tiptap h3 { font-size: 1.25em; font-weight: 600; margin: 0.8em 0 0.3em; font-family: 'Fira Code', monospace; }
.dark .tiptap-content .tiptap h1,
.dark .tiptap-content .tiptap h2,
.dark .tiptap-content .tiptap h3 { color: #fff; }

/* Paragraphs */
.tiptap-content .tiptap p { margin: 0.5em 0; }

/* Links */
.tiptap-content .tiptap a {
  color: #7b2fff;
  text-decoration: underline;
  cursor: pointer;
}
.dark .tiptap-content .tiptap a { color: #00d4ff; }

/* Lists */
.tiptap-content .tiptap ul { list-style: disc; padding-left: 1.5em; margin: 0.5em 0; }
.tiptap-content .tiptap ol { list-style: decimal; padding-left: 1.5em; margin: 0.5em 0; }
.tiptap-content .tiptap li { margin: 0.2em 0; }

/* Blockquote */
.tiptap-content .tiptap blockquote {
  border-left: 4px solid #7b2fff;
  background: rgba(123, 47, 255, 0.05);
  padding: 8px 16px;
  margin: 1em 0;
  border-radius: 0 12px 12px 0;
}
.dark .tiptap-content .tiptap blockquote {
  border-color: #00d4ff;
  background: rgba(0, 212, 255, 0.05);
}

/* Horizontal Rule */
.tiptap-content .tiptap hr {
  border: none;
  border-top: 2px solid #e5e7eb;
  margin: 1.5em 0;
}
.dark .tiptap-content .tiptap hr { border-color: #374151; }

/* Inline code */
.tiptap-content .tiptap code {
  background: rgba(123, 47, 255, 0.08);
  color: #7b2fff;
  padding: 2px 6px;
  border-radius: 6px;
  font-family: 'Fira Code', monospace;
  font-size: 0.85em;
}
.dark .tiptap-content .tiptap code {
  background: rgba(0, 212, 255, 0.1);
  color: #00d4ff;
}

/* ─── Code Blocks with Syntax Highlighting ─── */
.tiptap-content .tiptap pre {
  background: #0f172a;
  color: #e2e8f0;
  border: 1px solid #1e293b;
  border-radius: 12px;
  padding: 16px 20px;
  margin: 1em 0;
  overflow-x: auto;
  font-family: 'Fira Code', 'JetBrains Mono', monospace;
  font-size: 0.82rem;
  line-height: 1.7;
  tab-size: 2;
}
.dark .tiptap-content .tiptap pre {
  background: #020024;
  border-color: rgba(255,255,255,0.08);
}
.tiptap-content .tiptap pre code {
  background: none;
  color: inherit;
  padding: 0;
  border-radius: 0;
  font-size: inherit;
}

/* Highlight.js token colors (One Dark inspired) */
.tiptap-content .tiptap pre .hljs-keyword,
.tiptap-content .tiptap pre .hljs-selector-tag { color: #c678dd; }
.tiptap-content .tiptap pre .hljs-string,
.tiptap-content .tiptap pre .hljs-addition { color: #98c379; }
.tiptap-content .tiptap pre .hljs-number,
.tiptap-content .tiptap pre .hljs-literal { color: #d19a66; }
.tiptap-content .tiptap pre .hljs-comment,
.tiptap-content .tiptap pre .hljs-quote { color: #5c6370; font-style: italic; }
.tiptap-content .tiptap pre .hljs-function,
.tiptap-content .tiptap pre .hljs-title { color: #61afef; }
.tiptap-content .tiptap pre .hljs-variable,
.tiptap-content .tiptap pre .hljs-template-variable { color: #e06c75; }
.tiptap-content .tiptap pre .hljs-type,
.tiptap-content .tiptap pre .hljs-built_in { color: #e5c07b; }
.tiptap-content .tiptap pre .hljs-attr,
.tiptap-content .tiptap pre .hljs-attribute { color: #d19a66; }
.tiptap-content .tiptap pre .hljs-tag { color: #e06c75; }
.tiptap-content .tiptap pre .hljs-name { color: #e06c75; }
.tiptap-content .tiptap pre .hljs-selector-class,
.tiptap-content .tiptap pre .hljs-selector-id { color: #61afef; }
.tiptap-content .tiptap pre .hljs-meta { color: #56b6c2; }
.tiptap-content .tiptap pre .hljs-params { color: #abb2bf; }
.tiptap-content .tiptap pre .hljs-deletion { color: #e06c75; background: rgba(224,108,117,0.1); }
.tiptap-content .tiptap pre .hljs-regexp { color: #56b6c2; }
.tiptap-content .tiptap pre .hljs-symbol { color: #c678dd; }
.tiptap-content .tiptap pre .hljs-property { color: #e06c75; }
.tiptap-content .tiptap pre .hljs-punctuation { color: #abb2bf; }

/* Mark / Highlight */
.tiptap-content .tiptap mark {
  background: rgba(253, 224, 71, 0.4);
  border-radius: 2px;
  padding: 1px 2px;
}
.dark .tiptap-content .tiptap mark {
  background: rgba(253, 224, 71, 0.25);
}

/* Images */
.tiptap-content .tiptap img {
  max-width: 100%;
  height: auto;
  border-radius: 12px;
  margin: 1em 0;
}

/* YouTube / Video Embeds */
.tiptap-content .tiptap div[data-youtube-video] {
  position: relative;
  width: 100%;
  margin: 1em 0;
  border-radius: 12px;
  overflow: hidden;
}
.tiptap-content .tiptap div[data-youtube-video] iframe {
  width: 100%;
  aspect-ratio: 16 / 9;
  border: none;
  border-radius: 12px;
}
.tiptap-content .tiptap iframe {
  width: 100%;
  aspect-ratio: 16 / 9;
  border: none;
  border-radius: 12px;
  margin: 1em 0;
}

/* Tables */
.tiptap-content .tiptap table {
  border-collapse: collapse;
  width: 100%;
  margin: 1em 0;
  overflow: hidden;
  border-radius: 8px;
}
.tiptap-content .tiptap th,
.tiptap-content .tiptap td {
  border: 1px solid #e5e7eb;
  padding: 8px 12px;
  text-align: left;
  font-size: 0.85rem;
  min-width: 80px;
}
.tiptap-content .tiptap th {
  background: #f9fafb;
  font-weight: 600;
}
.dark .tiptap-content .tiptap th,
.dark .tiptap-content .tiptap td {
  border-color: #374151;
}
.dark .tiptap-content .tiptap th {
  background: rgba(55, 65, 81, 0.5);
}

/* Selected cell highlight */
.tiptap-content .tiptap .selectedCell::after {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(123, 47, 255, 0.08);
  pointer-events: none;
}
.dark .tiptap-content .tiptap .selectedCell::after {
  background: rgba(0, 212, 255, 0.08);
}
.tiptap-content .tiptap th,
.tiptap-content .tiptap td {
  position: relative;
}

/* Subscript / Superscript */
.tiptap-content .tiptap sub { font-size: 0.75em; vertical-align: sub; }
.tiptap-content .tiptap sup { font-size: 0.75em; vertical-align: super; }
</style>
