package org.develnext.jphp.ext.gui.desktop.classes;

import javafx.scene.input.MouseButton;
import org.develnext.jphp.ext.gui.desktop.GuiDesktopExtension;
import php.runtime.annotation.Reflection;
import php.runtime.annotation.Reflection.Getter;
import php.runtime.annotation.Reflection.Setter;
import php.runtime.annotation.Reflection.Signature;
import php.runtime.env.Environment;
import php.runtime.lang.BaseObject;
import php.runtime.lang.BaseWrapper;
import php.runtime.reflection.ClassEntity;

import java.awt.*;
import java.awt.datatransfer.Clipboard;
import java.awt.datatransfer.StringSelection;
import java.awt.event.InputEvent;
import java.awt.event.KeyEvent;

import static java.awt.event.KeyEvent.*;

@Reflection.Namespace(GuiDesktopExtension.NS)
public class Robot extends BaseWrapper<java.awt.Robot> {
    interface WrappedInterface {

    }

    public Robot(Environment env, ClassEntity clazz) {
        super(env, clazz);
    }

    public Robot(Environment env, java.awt.Robot wrappedObject) {
        super(env, wrappedObject);
    }

    @Signature
    public void __construct() throws AWTException {
        __wrappedObject = new java.awt.Robot();
    }

    @Getter
    public int getX() {
        return Mouse.x();
    }

    @Setter
    public void setX(int x) {
        getWrappedObject().mouseMove(x, Mouse.y());
    }

    @Getter
    public int getY() {
        return Mouse.y();
    }

    @Setter
    public void setY(int y) {
        getWrappedObject().mouseMove(Mouse.x(), y);
    }

    @Getter
    public int[] getPosition() {
        return new int[] { getX(), getY() };
    }

    @Setter
    public void setPosition(int[] pos) {
        if (pos.length >= 2) {
            setX(pos[0]);
            setY(pos[1]);
        }
    }

    @Signature
    public void mouseClick() {
        mouseClick(MouseButton.PRIMARY);
    }

    @Signature
    public void mouseClick(MouseButton button) {
        mouseDown(button);
        mouseUp(button);
    }

    @Signature
    public void mouseDown() {
        mouseDown(MouseButton.PRIMARY);
    }

    @Signature
    public void mouseDown(MouseButton button) {
        switch (button) {
            case PRIMARY:
                getWrappedObject().mousePress(InputEvent.BUTTON1_MASK);
                break;
            case MIDDLE:
                getWrappedObject().mousePress(InputEvent.BUTTON2_MASK);
                break;
            case SECONDARY:
                getWrappedObject().mousePress(InputEvent.BUTTON3_MASK);
                break;
        }
    }

    @Signature
    public void mouseUp() {
        mouseUp(MouseButton.PRIMARY);
    }

    @Signature
    public void mouseUp(MouseButton button) {
        switch (button) {
            case PRIMARY:
                getWrappedObject().mouseRelease(InputEvent.BUTTON1_MASK);
                break;
            case MIDDLE:
                getWrappedObject().mouseRelease(InputEvent.BUTTON2_MASK);
                break;
            case SECONDARY:
                getWrappedObject().mouseRelease(InputEvent.BUTTON3_MASK);
                break;
        }
    }

    @Signature
    public void mouseScroll(int wheelAmt) {
        getWrappedObject().mouseWheel(wheelAmt);
    }

    @Signature
    public void type(String characters) {
        Clipboard clipboard = Toolkit.getDefaultToolkit().getSystemClipboard();
        StringSelection stringSelection = new StringSelection( characters );
        clipboard.setContents(stringSelection, null);

        getWrappedObject().keyPress(KeyEvent.VK_CONTROL);
        getWrappedObject().keyPress(KeyEvent.VK_V);
        getWrappedObject().keyRelease(KeyEvent.VK_V);
        getWrappedObject().keyRelease(KeyEvent.VK_CONTROL);
    }
}
